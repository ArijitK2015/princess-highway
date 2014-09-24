<?php
/*
<crontab> 
	<jobs> 
		<factoryx_birthdaygift_send> 
			<schedule><cron_expr>0 1 * * *</cron_expr></schedule>
			<run><model>birthdaygift/observer::sendBirthdayEmail</model></run>
		</factoryx_birthdaygift_send> 
	</jobs> 
</crontab>
*/
include Mage::getBaseDir()."/lib/createsend/csrest_segments.php";

class FactoryX_BirthdayGift_Model_Observer extends Mage_Core_Model_Abstract 
{
	protected $ultimateRecipients = array();
	protected $emails = array();
	
	public function generateRecipientsArray($args)
	{	
		// If the customer email is in the CampaignMonitor array
		if (in_array($args['row']['email'],$this->emails))
		{
			continue;
		}
		// Fill a new array with the customer details
		$new_subscriber = array();
		$new_subscriber["fullname"] = $args['row']['name'];
		$new_subscriber["firstname"] = $args['row']['firstname'];
		$new_subscriber["email"] = $args['row']['email'];
		$this->ultimateRecipients[] = $new_subscriber;
	}
	
    public function __construct()
    {
		$this->_init('birthdaygift/observer');
    }

	/**
	 * Send birthday coupon via	email to customers
	 * @param string email (for testing purpose)
	 */
	public function sendBirthdayEmail($email = null) 
	{
		// GET ALL THE CONFIGURATIONS AND SEE IF WE HAVE ENOUGH TO WORK WITH?

		// sender name & email
		$sender = array();
		$sender['email'] = Mage::getStoreConfig('bdayconfig/options/email');
		$sender['name'] = Mage::getStoreConfig('bdayconfig/options/name');
		if (empty($sender['email'])) { Mage::helper('birthdaygift')->log(__METHOD__ . "Sender email not recognised."); return; }
		if (empty($sender['name'])) { Mage::helper('birthdaygift')->log(__METHOD__ . "Sender name not recognised."); return; }

		// email template
		$templateId = Mage::getStoreConfig('bdayconfig/options/template');		
		if (!is_numeric($templateId)) { Mage::helper('birthdaygift')->log(__METHOD__ . "Template ID is not numeric."); return; }
		$templateId = intval($templateId);

		// coupon prefix
		$prefix = Mage::getStoreConfig('bdayconfig/options/prefix');
		if (empty($prefix)) { Mage::helper('birthdaygift')->log(__METHOD__ . "Coupon prefix not recognised."); return; }		

		// coupon value
		$percentage = Mage::getStoreConfig('bdayconfig/options/value');
		if (!is_numeric($percentage)) { Mage::helper('birthdaygift')->log(__METHOD__ . "Coupon value not recognised."); return; }		
		$percentage = intval($percentage);

		// coupon valid for
		$validdays = Mage::getStoreConfig('bdayconfig/options/valid');
		if (!is_numeric($validdays)) { Mage::helper('birthdaygift')->log(__METHOD__ . "Coupon validity not recognised."); return; }		
		$validdays = intval($validdays);

		// send to campaign monitor?
		// $cm_enable = Mage::getStoreConfig('bdayconfig/options/campaignmonitor');
		// $cm_enable = intval($cm_enable);		

		// if yes do we have the things we need?
		// $segmentID = Mage::getStoreConfig('bdayconfig/options/segmentID');

		// print_r($sender);echo $templateId;echo $prefix;echo $percentage; echo $validdays; echo $cm_enable;echo $segmentID;return;
		
		// Get the CampaignMonitor Details
		$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
		//$listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
		// AH PRODUCTION SEGMENT
		$segID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/segmentID'));

		// try 
		// {
		// 	$subscriber_controller = new CS_REST_Segments($segID,$apiKey);
		// 	$size = 50; 
		// 	$page = 0;
		// 	//$tmp = $subscriber_controller->get_subscribers('',1,50); 
		// 	$today = Mage::getModel('core/date')->date('m').'-'.Mage::getModel('core/date')->date('d');
		 
		// 	do 
		// 	{
		// 		$tmp = $subscriber_controller->get_subscribers('',++$page,$size);       
		// 		foreach ($tmp->response->Results as $subscriber)
		// 		{
		// 			$not_today = true;
		// 			foreach ($subscriber->CustomFields as $field)
		// 			{
		// 				if ($field->Key == "Date Of Birth")
		// 				{
		// 					if (substr($field->Value,5,5) == $today)
		// 					{
		// 						$not_today = false;
		// 						break;
		// 					}
		// 				}
		// 			}
		// 			if ($not_today) continue;
		// 			$new_subscriber = array();
		// 			$new_subscriber["fullname"] = $subscriber->Name;
		// 			$new_subscriber["firstname"] = current(explode(' ', $subscriber->Name));
		// 			$new_subscriber["email"] = $subscriber->EmailAddress;
		// 			$ultimate_array[] = $new_subscriber;
		// 			$this->emails[] = $subscriber->EmailAddress;
		// 		}
		// 	} while (sizeof($tmp->response->Results) == $size);
		// }catch (Exception $e) {
		// 	Mage::helper('birthdaygift')->log($e->getMessage());
		// }
		
		// Get the transactional email template
		$templateId = Mage::getStoreConfig('customer/birthdaygift/email_template');
		
		// Get the sender
		$sender = array();
		$sender['email'] = Mage::getStoreConfig('trans_email/birthdaygift/email');
		$sender['name'] = Mage::getStoreConfig('trans_email/birthdaygift/name');
		
		// Create an array of variables to assign to template 
		$emailTemplateVariables = array(); 
		
		// Get the customers collection
		$customer = Mage::getModel("customer/customer")->getCollection();

		// Get today's date
		$today = '%'.Mage::getModel('core/date')->date('m').'-'.Mage::getModel('core/date')->date('d').' 00:00:00';
		
		// If the email is set, it is for testing purpose
		
		if ($email != null)
		{
			// Get the model
			$customer = Mage::getModel("customer/customer");
			// 1 for Alannah Hill
			$customer->setWebsiteId(1);
			// Load customer by email
			$customer->loadByEmail($email);
			// Get date of birth
			$dob = $customer->getDob();
			// Remove the year
			$dob = '%'.substr($dob,5);
			// Compare both dates
			if ($dob === $today)
			{
				Mage::helper('birthdaygift')->log(__METHOD__ . "test sendBirthdayEmail: " . $customer->getEmail());
				
				// Array that contains the data which will be used inside the template
				$emailTemplateVariables['fullname'] = $customer->getName(); 
				$emailTemplateVariables['firstname'] = $customer->getFirstname();
				$emailTemplateVariables['bday_code'] = $this->create_salesrule($customer->getEmail(),$percentage,$prefix, $validdays); 
				
				// Send the email
				$email = $customer->getEmail();
				Mage::getModel('core/email_template')
						->sendTransactional(
								$templateId,
								$sender,
								$email,
								$customer->getName(),
								$emailTemplateVariables,
								null);
			}
		}
		else
		{
			// Filter the collection by date of birth
			$customer->addFieldToFilter('dob', array('like' => $today));
			$customer->addNameToSelect(); 
			
			// Call iterator walk method with collection query string and callback method as parameters
			// Has to be used to handle massive collection instead of foreach
			Mage::getSingleton('core/resource_iterator')->walk($customer->getSelect(), array(array($this, 'generateRecipientsArray')));

			// Loop through the ultimate filtered array
			foreach ($this->ultimateRecipients as $item)
			{
				// Mage::helper('birthdaygift')->log(__METHOD__ . "sendBirthdayEmail: " . $item["email"]);
				
				// Array that contains the data which will be used inside the template
				$emailTemplateVariables['fullname'] = $item["fullname"]; 
				$emailTemplateVariables['firstname'] = $item["firstname"];
				
				if ($item["email"] != "null@factoryx.com.au")
				{
					$emailTemplateVariables['bday_code'] = $this->create_salesrule($item["email"],$percentage,$prefix, $validdays);
					try 
					{
						//Send the email
						Mage::getModel('core/email_template')
								->sendTransactional(
										$templateId,
										$sender,
										$item["email"],
										$item["fullname"],
										$emailTemplateVariables,
										null);
		
					}
					catch (Exception $e) {
						Mage::helper('birthdaygift')->log(__METHOD__ . " " . $e->getMessage());
					}
				}
			}
		}
		return $this; 
	}

	/*
	 * Create new coupon code
	 * @param 	string 		$text, int $percentage, string $prefix
	 * @return 	Mage_SalesRule_Model_Rule
	 */
	public function create_salesrule($text = "Unknown", $percentage, $prefix, $validdays) 
	{
		// Name of the rule
		$name = sprintf("birthday coupon for %s", $text);
		$description = $name;
		
		// Validity for two weeks
		$ts = Mage::getModel('core/date')->timestamp(time());
        $fromDate = date('Y-m-d', $ts);
		
		// Add two weeks
		$toDate = date('Y-m-d', strtotime(sprintf("%s +%d days", date("Y-m-d", $ts), $validdays)) );
		
		$description .= sprintf(" valid [%s - %s]", date("Y-m-d H:i:s", $ts), date('Y-m-d H:i:s', strtotime(sprintf("%s +%d days", date("Y-m-d", $ts), $validdays)) ));
		
		// Generate the hash code for the coupon
		$coupon_code = sprintf("%s_%s", $prefix, $this->generateHashCode());
		
		// Create the coupon and save it
    	$coupon = Mage::getModel('salesrule/rule');
    	$coupon->setName($name)
    		->setDescription($description)
    		->setFromDate($fromDate)
    		->setToDate($toDate)
    		->setCouponCode($coupon_code)
    		->setUsesPerCoupon(1)
    		->setUsesPerCustomer(1)
    		->setCustomerGroupIds(array(0,1,2,3))
    		->setCouponType('2')
    		->setIsActive(1)
    		->setConditionsSerialized('a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
    		->setActionsSerialized('a:7:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:3:"sku";s:8:"operator";s:3:"!{}";s:5:"value";s:4:"gift";s:18:"is_value_processed";b:0;}}}')
    		->setStopRulesProcessing(1)
    		->setIsAdvanced(0)
    		->setProductIds('')
    		->setSortOrder(0)
    		->setSimpleAction('by_percent')
    		->setDiscountAmount($percentage)
    		->setDiscountQty(null)
    		->setDiscountStep('0')
    		->setSimpleFreeShipping('0')
    		->setApplyToShipping('0')
    		->setIsRss(0)
    		->setWebsiteIds(array(1));	// 1 = Princess Highway
    	$coupon->save(); 		
		
		return $coupon_code;
	}

	/**
	 * Generate Hash Code for the coupon
	 * @return string
	 */
	private function generateHashCode() 
	{
		$seed = 'JvKnrQWPsThuJteNQAuH' + date("Y-m-d");
		$hash = sha1(uniqid($seed . mt_rand(), true));
		// To get a shorter version of the hash, just use substr
		return substr(strtoupper($hash), 0, 7);
	}

}