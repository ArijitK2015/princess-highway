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
		if (empty($sender['email'])) { Mage::helper('birthdaygift')->log(__METHOD__ . "Birthday Gift Module: Sender email not recognised."); return; }
		if (empty($sender['name'])) { Mage::helper('birthdaygift')->log(__METHOD__ . "Birthday Gift Module: Sender name not recognised."); return; }

		// email template
		$templateId = Mage::getStoreConfig('bdayconfig/options/template');		
		if (!is_numeric($templateId)) { Mage::helper('birthdaygift')->log(__METHOD__ . "Birthday Gift Module: Template ID is not numeric."); return; }
		$templateId = intval($templateId);

		// coupon prefix
		$prefix = Mage::getStoreConfig('bdayconfig/options/prefix');
		if (empty($prefix)) { Mage::helper('birthdaygift')->log(__METHOD__ . "Birthday Gift Module: Coupon prefix not recognised."); return; }		

		// coupon value
		$percentage = Mage::getStoreConfig('bdayconfig/options/value');
		if (!is_numeric($percentage)) { Mage::helper('birthdaygift')->log(__METHOD__ . "Birthday Gift Module: Coupon value not recognised."); return; }		
		$percentage = intval($percentage);

		// coupon valid for
		$validdays = Mage::getStoreConfig('bdayconfig/options/valid');
		if (!is_numeric($validdays)) { Mage::helper('birthdaygift')->log(__METHOD__ . "Birthday Gift Module: Coupon validity not recognised."); return; }		
		$validdays = intval($validdays);

		// send to campaign monitor?
		// $cm_enable = Mage::getStoreConfig('bdayconfig/options/campaignmonitor');
		// $cm_enable = intval($cm_enable);		

		// if yes do we have the things we need?
		// $segmentID = Mage::getStoreConfig('bdayconfig/options/segmentID');

		// print_r($sender);echo $templateId;echo $prefix;echo $percentage; echo $validdays; echo $cm_enable;echo $segmentID;return;

		// Declare the arrays
		$ultimate_array = array();
		$email_array = array();
		
		// // Get the CampaignMonitor Details
		// $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
		// //$listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));


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
		// 			$email_array[] = $subscriber->EmailAddress;
		// 		}
		// 	} while (sizeof($tmp->response->Results) == $size);
		// }catch (Exception $e) {
		// 	Mage::helper('birthdaygift')->log(__METHOD__ . $e->getMessage());
		// }

		
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
			// 3 for Dangerfield
			$customer->setWebsiteId(3);
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
				Mage::getModel('core/email_template')
						->sendTransactional(
								$templateId,
								$sender,
								$customer->getEmail(),
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
			
			// Get the filtered customers
			$items = $customer->getItems();
			
			foreach($items as $item) 
			{
				// If the customer email is in the CampaignMonitor array
				if (in_array($item->getEmail(),$email_array))
				{
					continue;
				}
				// Fill a new array with the customer details
				$new_subscriber = array();
				$new_subscriber["fullname"] = $item->getName();
				$new_subscriber["firstname"] = $item->getFirstname();
				$new_subscriber["email"] = $item->getEmail();
				$ultimate_array[] = $new_subscriber; 
			}

			// Loop through the ultimate filtered array
			foreach ($ultimate_array as $item)
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
						// Send the email
						Mage::getModel('core/email_template')
								->sendTransactional(
										$templateId,
										$sender,
										$item["email"],
										$item["fullname"],
										$emailTemplateVariables,
										null);
						// Send Alvin the email
						Mage::getModel('core/email_template')
								->sendTransactional(
										$templateId,
										$sender,
										'alvin@alvinnguyen.net',
										$item["fullname"],
										$emailTemplateVariables,
										null);												
					}catch (Exception $e) {
						Mage::helper('birthdaygift')->log(__METHOD__ . $e->getMessage());
					}
				}
			}
		}
		return $this; 
	}

	/*
	 * Create new coupon code
	 * @param 	string 		$text
	 * @return 	Mage_SalesRule_Model_Rule
	 */
	public function create_salesrule($text = "Unknown",$percentage, $prefix, $validdays) 
	{
		// Name of the rule
		$name = sprintf("birthday coupon for %s", $text);
		$description = $name;
		
		// Validity for two weeks
		$from_date = date("Y-m-d");
		
		// Add two weeks
		$to_date = date('Y-m-d', strtotime(date("Y-m-d", strtotime($from_date)) . " +".$validdays." days"));
		
		// Generate the hash code for the coupon
		$coupon_code = $prefix."_".$this->generateHashCode();
		
		// Create the coupon and save it
    	$coupon = Mage::getModel('salesrule/rule');
    	$coupon->setName($name)
    		->setDescription($description)
    		->setFromDate($from_date)
    		->setToDate($to_date)
    		->setCouponCode($coupon_code)
    		->setUsesPerCoupon(1)
    		->setUsesPerCustomer(1)
    		->setCustomerGroupIds(array(0,1,2,3))
    		->setCouponType('2')
    		->setIsActive(1)
    		->setConditionsSerialized('a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
    		->setActionsSerialized('a:6:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
    		->setStopRulesProcessing(0)
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
    		->setWebsiteIds(array(1,3,4)); // 1 = Main Website 3 = Dangerfield Online 4 = Pulp Kitchen
		
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