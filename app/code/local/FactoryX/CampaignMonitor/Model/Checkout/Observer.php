<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_subscribers.php";
class FactoryX_CampaignMonitor_Model_Checkout_Observer
{
    public function subscribeCustomer($observer)
    {
		// Check if the checkbox has been ticked using the sessions
        if ((bool) Mage::getSingleton('checkout/session')->getCustomerIsSubscribed())
		{
			// Get the quote & customer
            $quote = $observer->getEvent()->getQuote();
            $order = $observer->getEvent()->getOrder();     

            // Get the email using during checking out
			$email = $order->getCustomerEmail();	     	
          	            
            $customer = $quote->getCustomer();
			$session = Mage::getSingleton('core/session');
			
			// We get the API and List ID
			$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
			$listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
			
			// Variables 
			$firstname = "";
			$lastname = "";
			$mobile = "";
			$state = "";
			$postcode = "";
			$jobinterest = "";
			$periodicity = "";
			$dob = "";
			$subscriptiondate = date("Y-m-d");

			// Check if already susbcribed
			try {
				$client = new CS_REST_Subscribers($listID,$apiKey);
				$result = $client->get($email);
			} catch(Exception $e) {
				Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in REST call: ".$e->getMessage());
				$session->addException($e, Mage::helper('campaignmonitor')->__('There was a problem with the subscription'));
			}
			
			// If we are not subscribed in Campaign Monitor
			if ($result->was_successful() && $result->response->State != 'Active')
			{
				// Mage::helper('campaignmonitor')->log($quote->getBillingAddress());
				// We generate the custom fields
				if ($mobile = $quote->getBillingAddress()->getTelephone())
				{
					$customFields[] = array("Key" => "Mobile", "Value" => $mobile);
				}
				$state = $quote->getBillingAddress()->getRegion();
				$country = $quote->getBillingAddress()->getCountryId();
				if ($state || $country)
				{
					$campaignMonitorStates = Mage::helper('campaignmonitor')->getCampaignMonitorStates();
					if ($country == "AU" && in_array($state,$campaignMonitorStates))
					{
						$customFields[] = array("Key" => "State", "Value" => $state);
					}
					elseif($country == "NZ")
					{
						$customFields[] = array("Key" => "State", "Value" => "New Zealand");
					}
					elseif($country)
					{
						$customFields[] = array("Key" => "State", "Value" => "Other");
					}
					else
					{
						$customFields[] = array("Key" => "State", "Value" => "Unknown");
					}
				}
				else
				{
					$customFields[] = array("Key" => "State", "Value" => "Unknown");
				}
				if ($postcode = $quote->getBillingAddress()->getPostcode())
				{
					$customFields[] = array("Key" => "Postcode", "Value" => $postcode);
				}
				if ($dob = $quote->getCustomerDob())
				{
					$customFields[] = array("Key" => "DateOfBirth", "Value" => $dob);
				}
				// And generate the hash
				$customFields[] = array("Key" => "securehash", "Value" => md5($email.$apiKey));
				// We generate the Magento fields
				$fullname = $quote->getBillingAddress()->getName();
				$fullname = trim($fullname);
				$names = explode(" ", $fullname, 2);
				if (!empty($names) && isset($names[0]) && isset($names[1])) 
				{
					$firstname = (string) $names[0];
					$lastname = (string) $names[1];
				}
				else 
				{
					$firstname = $fullname;
					$lastname = "";
				}
				$customFields[] = array("Key" => "fullname", "Value" => $fullname);
				
				// Check the checkout method (logged in, register or guest)
				switch ($quote->getCheckoutMethod())
				{
					// Customer is logged in
					case Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN:					
					// Customer is registering
					case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER:
					// Customer is a guest
					case Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST:
						try {
							// Subscribe the customer to CampaignMonitor
							if($client) {
									$result = $client->add(array(
												"EmailAddress" => $email,
												"Name" => $fullname,
												"CustomFields" => $customFields,
												"Resubscribe" => true));
							}
						}
						catch (Exception $e) 
						{
							Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in CampaignMonitor REST call: ".$e->getMessage());
							$session->addException($e, Mage::helper('campaignmonitor')->__('There was a problem with the subscription'));
						}
						break;
				}
			}
			
			// Check if already subscribed in Magento
			$subscriber = Mage::getModel('campaignmonitor/subscriber')->loadByEmail($email);
			
			if (!$subscriber || !$subscriber->isSubscribed()) 
			{
				// Magento subscription
				try
				{
					$status = Mage::getModel('campaignmonitor/subscriber')->subscribeWithDetails($email, $firstname, $lastname, $mobile, $state, $periodicity, $jobinterest, $dob, $subscriptiondate, $postcode);
					if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) 
					{
						$session->addSuccess(Mage::helper('campaignmonitor')->__('Confirmation request has been sent.'));
					}
					else 
					{
						$session->addSuccess(Mage::helper('campaignmonitor')->__('Thank you for your subscription.'));
					}
				}
				catch (Mage_Core_Exception $e) {
					$session->addException($e, Mage::helper('campaignmonitor')->__('There was a problem with the newsletter subscription: %s', $e->getMessage()));
				}
				catch (Exception $e) {
					$session->addException($e, Mage::helper('campaignmonitor')->__('There was a problem with the newsletter subscription'));
				}
			}
			// Remove the session variable
			Mage::getSingleton('checkout/session')->setCustomerIsSubscribed(0);
        }
    }
}