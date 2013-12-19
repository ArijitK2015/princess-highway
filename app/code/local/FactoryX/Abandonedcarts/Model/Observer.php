<?php

class FactoryX_Abandonedcarts_Model_Observer extends Mage_Core_Model_Abstract 
{

	/**
	 * Send notification email to customer with abandoned cart containing sale products
	 * @param boolean if dryrun is set to true, it won't send emails and won't alter quotes
	 * @param string email to test
	 */
	public function sendAbandonedCartsSaleEmail($dryrun = false, $testemail = null) 
	{
		try
		{
	
			// Date handling	
			$store = Mage_Core_Model_App::ADMIN_STORE_ID;
			$timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
			date_default_timezone_set($timezone);
		
			// Current date
			$currentdate = date("Ymd");

			$day = (int)substr($currentdate,-2);
			$month = (int)substr($currentdate,4,2);
			$year = (int)substr($currentdate,0,4);

			$date = array('year' => $year,'month' => $month,'day' => $day,'hour' => 23,'minute' => 59,'second' => 59);
			
			$today = new Zend_Date($date);
			$today->setTimeZone("UTC");

			date_default_timezone_set($timezone);

			$todayString = $today->toString("Y-MM-dd HH:mm:ss");
			
			// Get the attribute id for the status attribute
			$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
			$statusId = $eavAttribute->getIdByCode('catalog_product', 'status');
			
			// Loop through the stores
			foreach (Mage::app()->getWebsites() as $website) {
				// Get the website id
				$websiteId = $website->getWebsiteId();
				foreach ($website->getGroups() as $group) {
					$stores = $group->getStores();
					foreach ($stores as $store) {
					
						// Get the store id
						$storeId = $store->getStoreId();
			
						// Init the store to be able to load the quote and the collections properly
						Mage::app()->init($storeId,'store');
						
						// Get the product collection
						$collection = Mage::getResourceModel('catalog/product_collection')->setStore($storeId);
						
						// First collection: carts with products that became on sale
						// Join the collection with the required tables
						$collection->getSelect()
							->reset(Zend_Db_Select::COLUMNS)
							->columns(array('e.entity_id AS product_id',
											'e.sku',
											'catalog_flat.name as product_name',
											'catalog_flat.price as product_price',
											'catalog_flat.special_price as product_special_price',
											'catalog_flat.special_from_date as product_special_from_date',
											'catalog_flat.special_to_date as product_special_to_date',
											'quote_table.entity_id as cart_id',
											'quote_table.updated_at as cart_updated_at',
											'quote_table.abandoned_sale_notified as has_been_notified',
											'quote_items.price as product_price_in_cart',
											'quote_table.customer_email as customer_email',
											'quote_table.customer_firstname as customer_firstname',
											'quote_table.customer_lastname as customer_lastname'
											)
										)
							->joinInner(
								array('quote_items' => 'sales_flat_quote_item'),
								'quote_items.product_id = e.entity_id AND quote_items.price > 0.00',
								null)
							->joinInner(
								array('quote_table' => 'sales_flat_quote'),
								'quote_items.quote_id = quote_table.entity_id AND quote_table.items_count > 0 AND quote_table.is_active = 1 AND quote_table.customer_email IS NOT NULL AND quote_table.abandoned_sale_notified = 0 AND quote_table.store_id = '.$storeId,
								null)
							->joinInner(
								array('catalog_flat' => 'catalog_product_flat_'.$storeId),
								'catalog_flat.entity_id = e.entity_id',
								null)
							->joinInner(
								array('catalog_enabled'	=>	'catalog_product_entity_int'),
								'catalog_enabled.entity_id = e.entity_id AND catalog_enabled.attribute_id = '.$statusId.' AND catalog_enabled.value = 1',
								null)
							->joinInner(
								array('inventory' => 'cataloginventory_stock_status'),
								'inventory.product_id = e.entity_id AND inventory.stock_status = 1 AND inventory.website_id = '.$websiteId,
								null)
							->order('quote_table.updated_at DESC');
												
						//echo $collection->printlogquery(true);
						$collection->load();
						
						// Skip the rest of the code if the collection is empty
						if (count($collection) == 0)
						{
							continue;
						}
						
						// Recipients array
						$recipients = array();
						
						foreach($collection as $entry)
						{
							// Double check if the special from date is set
							if (!$entry->getProductSpecialFromDate())
							{
								// If not we use today for the comparison
								$fromDate = $todayString;
							}
							else $fromDate = $entry->getProductSpecialFromDate();
							
							// Do the same for the special to date
							if (!$entry->getProductSpecialToDate())
							{
								$toDate = $todayString;
							}
							else $toDate = $entry->getProductSpecialToDate();
							
							// We need to ensure that the price in cart is higher than the new special price
							// As well as the date comparison in case the sale is over or hasn't started
							if ($entry->getProductPriceInCart() > 0.00 
								&& $entry->getProductSpecialPrice() > 0.00 
								&& ($entry->getProductPriceInCart() > $entry->getProductSpecialPrice())
								&& ($fromDate <= $todayString)
								&& ($toDate >= $todayString))
							{
								
								// Test if the customer is already in the array
								if (!array_key_exists($entry->getCustomerEmail(), $recipients))
								{
									// Create an array of variables to assign to template 
									$emailTemplateVariables = array(); 
									
									// Array that contains the data which will be used inside the template
									$emailTemplateVariables['fullname'] = $entry->getCustomerFirstname().' '.$entry->getCustomerLastname(); 
									$emailTemplateVariables['firstname'] = $entry->getCustomerFirstname();
									$emailTemplateVariables['productname'] = $entry->getProductName(); 
									$emailTemplateVariables['cartprice'] = number_format($entry->getProductPriceInCart(),2); 
									$emailTemplateVariables['specialprice'] = number_format($entry->getProductSpecialPrice(),2);
									
									// Assign the values to the array of recipients
									$recipients[$entry->getCustomerEmail()]['cartId'] = $entry->getCartId();
								}
								else
								{
									// We create some extra variables if there is several products in the cart
									$emailTemplateVariables = $recipients[$entry->getCustomerEmail()]['emailTemplateVariables'];
									// Discount amount
									// If one product before
									if (!array_key_exists('discount',$emailTemplateVariables))
									{
										$emailTemplateVariables['discount'] = $emailTemplateVariables['cartprice'] - $emailTemplateVariables['specialprice'];
									}
									// We add the discount on the second product
									$moreDiscount = number_format($entry->getProductPriceInCart(),2) - number_format($entry->getProductSpecialPrice(),2);
									$emailTemplateVariables['discount'] += $moreDiscount;
									// We increase the product count
									if (!array_key_exists('extraproductcount',$emailTemplateVariables))
									{
										$emailTemplateVariables['extraproductcount'] = 0;
									}
									$emailTemplateVariables['extraproductcount'] += 1;
								}
								// Assign the array of template variables
								$recipients[$entry->getCustomerEmail()]['emailTemplateVariables'] = $emailTemplateVariables;
							}
						}
						
						// Get the transactional email template
						$templateId = Mage::getStoreConfig('abandonedcartsconfig/options/email_template_sale');
						// Get the sender
						$sender = array();
						$sender['email'] = Mage::getStoreConfig('abandonedcartsconfig/options/email');
						$sender['name'] = Mage::getStoreConfig('abandonedcartsconfig/options/name');
						
						// Send the emails via a loop
						foreach ($recipients as $email => $recipient)
						{
							// Don't send the email if dryrun is set
							if ($dryrun)
							{
								// Log data when dried run
								Mage::helper('abandonedcarts')->log(print_r($recipient['emailTemplateVariables'],true));
								// If the test email is set and found
								if (isset($testemail) && $email == $testemail)
								{
									Mage::helper('abandonedcarts')->log(__METHOD__ . "sendAbandonedCartsSaleEmail test: " . $email);
									// Send the test email
									Mage::getModel('core/email_template')
											->sendTransactional(
													$templateId,
													$sender,
													$email,
													$recipient['emailTemplateVariables']['fullname'] ,
													$recipient['emailTemplateVariables'],
													null);
								}
							}
							else
							{
								Mage::helper('abandonedcarts')->log(__METHOD__ . "sendAbandonedCartsSaleEmail: " . $email);
								
								// Send the email
								Mage::getModel('core/email_template')
										->sendTransactional(
												$templateId,
												$sender,
												$email,
												$recipient['emailTemplateVariables']['fullname'] ,
												$recipient['emailTemplateVariables'],
												null);
							}
							
							// Load the quote
							$quote = Mage::getModel('sales/quote')->load($recipient['cartId']);

							// We change the notification attribute
							$quote->setAbandonedSaleNotified(1);
							
							// Save only if dryrun is false or if the test email is set and found
							if (!$dryrun || (isset($testemail) && $email == $testemail))
							{
								$quote->save();
							}
						}
					}
				}
			}
		}
		catch (Exception $e)
		{
			Mage::helper('abandonedcarts')->log(__METHOD__ . $e->getMessage());
		}
		
	}
	
	/**
	 * Send notification email to customer with abandoned carts after the number of days specified in the config
	 * @param boolean if dryrun is set to true, it won't send emails and won't alter quotes
	 * @param string email to test
	 */
	public function sendAbandonedCartsEmail($dryrun = false, $testemail = null)
	{
		try
		{
	
			// Date handling	
			$store = Mage_Core_Model_App::ADMIN_STORE_ID;
			$timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
			date_default_timezone_set($timezone);
			
			// Get the delay provided and convert it to a proper date
			$delay = Mage::getStoreConfig('abandonedcartsconfig/options/notify_delay');
			$delay = date('Y-m-d', time() - $delay * 24 * 3600);
			
			// Get the attribute id for the status attribute
			$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
			$statusId = $eavAttribute->getIdByCode('catalog_product', 'status');
			
			// Loop through the stores
			foreach (Mage::app()->getWebsites() as $website) {
				// Get the website id
				$websiteId = $website->getWebsiteId();
				foreach ($website->getGroups() as $group) {
					$stores = $group->getStores();
					foreach ($stores as $store) {
					
						// Get the store id
						$storeId = $store->getStoreId();
						// Init the store to be able to load the quote and the collections properly
						Mage::app()->init($storeId,'store');
						
						// Get the product collection
						$collection = Mage::getResourceModel('catalog/product_collection')->setStore($storeId);
						
						// First collection: carts with products that became on sale
						// Join the collection with the required tables
						$collection->getSelect()
							->reset(Zend_Db_Select::COLUMNS)
							->columns(array('e.entity_id AS product_id',
											'e.sku',
											'catalog_flat.name as product_name',
											'catalog_flat.price as product_price',
											'quote_table.entity_id as cart_id',
											'quote_table.updated_at as cart_updated_at',
											'quote_table.abandoned_notified as has_been_notified',
											'quote_table.customer_email as customer_email',
											'quote_table.customer_firstname as customer_firstname',
											'quote_table.customer_lastname as customer_lastname'
											)
										)
							->joinInner(
								array('quote_items' => 'sales_flat_quote_item'),
								'quote_items.product_id = e.entity_id AND quote_items.price > 0.00',
								null)
							->joinInner(
								array('quote_table' => 'sales_flat_quote'),
								'quote_items.quote_id = quote_table.entity_id AND quote_table.items_count > 0 AND quote_table.is_active = 1 AND quote_table.customer_email IS NOT NULL AND quote_table.abandoned_notified = 0 AND quote_table.updated_at < "'.$delay.'" AND quote_table.store_id = '.$storeId,
								null)
							->joinInner(
								array('catalog_flat' => 'catalog_product_flat_'.$storeId),
								'catalog_flat.entity_id = e.entity_id',
								null)
							->joinInner(
								array('catalog_enabled'	=>	'catalog_product_entity_int'),
								'catalog_enabled.entity_id = e.entity_id AND catalog_enabled.attribute_id = '.$statusId.' AND catalog_enabled.value = 1',
								null)
							->joinInner(
								array('inventory' => 'cataloginventory_stock_status'),
								'inventory.product_id = e.entity_id AND inventory.stock_status = 1 AND website_id = '.$websiteId,
								null)
							->order('quote_table.updated_at DESC');
						
						//echo $collection->printlogquery(true);
						$collection->load();
						
						// Recipients array
						$recipients = array();
						
						foreach($collection as $entry)
						{
							// Test if the customer is already in the array
							if (!array_key_exists($entry->getCustomerEmail(), $recipients))
							{
								// Create an array of variables to assign to template 
								$emailTemplateVariables = array(); 
								
								// Array that contains the data which will be used inside the template
								$emailTemplateVariables['fullname'] = $entry->getCustomerFirstname().' '.$entry->getCustomerLastname(); 
								$emailTemplateVariables['firstname'] = $entry->getCustomerFirstname();
								$emailTemplateVariables['productname'] = $entry->getProductName();
										
								// Assign the values to the array of recipients
								$recipients[$entry->getCustomerEmail()]['cartId'] = $entry->getCartId();
							}
							else
							{
								// We create some extra variables if there is several products in the cart
								$emailTemplateVariables = $recipients[$entry->getCustomerEmail()]['emailTemplateVariables'];
								// We increase the product count
								if (!array_key_exists('extraproductcount',$emailTemplateVariables))
								{
									$emailTemplateVariables['extraproductcount'] = 0;
								}
								$emailTemplateVariables['extraproductcount'] += 1;
							}
							// Assign the array of template variables
							$recipients[$entry->getCustomerEmail()]['emailTemplateVariables'] = $emailTemplateVariables;
						}
						
						// Get the transactional email template
						$templateId = Mage::getStoreConfig('abandonedcartsconfig/options/email_template');
						// Get the sender
						$sender = array();
						$sender['email'] = Mage::getStoreConfig('abandonedcartsconfig/options/email');
						$sender['name'] = Mage::getStoreConfig('abandonedcartsconfig/options/name');
						
						// Send the emails via a loop
						foreach ($recipients as $email => $recipient)
						{
							// Don't send the email if dryrun is set
							if ($dryrun)
							{
								// Log data when dried run
								Mage::helper('abandonedcarts')->log(print_r($recipient['emailTemplateVariables'],true));
								// If the test email is set and found
								if (isset($testemail) && $email == $testemail)
								{
									Mage::helper('abandonedcarts')->log(__METHOD__ . "sendAbandonedCartsEmail test: " . $email);
									// Send the test email
									Mage::getModel('core/email_template')
											->sendTransactional(
													$templateId,
													$sender,
													$email,
													$recipient['emailTemplateVariables']['fullname'] ,
													$recipient['emailTemplateVariables'],
													null);
								}
							}
							else
							{
								Mage::helper('abandonedcarts')->log(__METHOD__ . "sendAbandonedCartsEmail: " . $email);
								
								// Send the email
								Mage::getModel('core/email_template')
										->sendTransactional(
												$templateId,
												$sender,
												$email,
												$recipient['emailTemplateVariables']['fullname'] ,
												$recipient['emailTemplateVariables'],
												null);
							}
							
							// Load the quote
							$quote = Mage::getModel('sales/quote')->load($recipient['cartId']);

							// We change the notification attribute
							$quote->setAbandonedNotified(1);
							
							// Save only if dryrun is false or if the test email is set and found
							if (!$dryrun || (isset($testemail) && $email == $testemail))
							{
								$quote->save();
							}
						}
					}
				}
			}
		}
		catch (Exception $e)
		{
			Mage::helper('abandonedcarts')->log(__METHOD__ . $e->getMessage());
		}
		
	}
}