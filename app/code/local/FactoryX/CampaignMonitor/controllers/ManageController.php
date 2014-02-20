<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_subscribers.php";
include_once Mage::getModuleDir('controllers','Mage_Newsletter').DS."ManageController.php";

class FactoryX_CampaignMonitor_ManageController extends Mage_Newsletter_ManageController
{
	/**
	 *	NOT IN USE
	 *	Originally use to be able to update newsletter subscription from the manage subscription page
	 *	Kept in case of rollback
	 */
    public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {
            Mage::getSingleton('customer/session')->getCustomer()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
            ->save();
			
			// Load the subscriber
			$email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
			
			// Get the new data
			$new_email = (string) $this->getRequest()->getParam('email');
			$state = (string) $this->getRequest()->getParam('state');
			$postcode = (string) $this->getRequest()->getParam('postcode');
			$mobilesubscription = ($this->getRequest()->getParam('is_subscribed_sms')==1)?"YES":"NO";
			$mobile = (string) $this->getRequest()->getParam('mobile');
			$preferred_store = (string) $this->getRequest()->getParam('preferred_store');
			$periodicity = (string) $this->getRequest()->getParam('periodicity');
			
			// Handle Campaign Monitor fields update
			
			Mage::helper('campaignmonitor')->log("Updating newsletter subscription custom fiels via frontend 'Manage Subscription' page for $email");
			
			$customerHelper = Mage::helper('customer');
			$customer = $customerHelper->getCustomer();
			$name = $customer->getFirstname() . " " . $customer->getLastname();
			$customFields = FactoryX_CampaignMonitor_Model_Customer_Observer::generateCustomFields($customer);
			
			if ($mobilesubscription=="YES" && $mobile && isset($mobile))
			{
				$customFields[] = array("Key" => "Mobile", "Value" => $mobile);
			}
			if ($email && isset($email))
			{
				$customFields[] = array("Key" => "email", "Value" => $email);
			}
			if ($state != "-1" && isset($state))
			{
				$customFields[] = array("Key" => "State", "Value" => $state);
			}
			if ($postcode && isset($postcode))
			{
				$customFields[] = array("Key" => "Postcode", "Value" => $postcode);
			}
			if ($periodicity != "-1" && isset($periodicity))
			{
				$customFields[] = array("Key" => "Frequency", "Value" => $periodicity);
			}
			if ($preferred_store != "-1" && isset($preferred_store))
			{
				$customFields[] = array("Key" => "PreferredStore/number", "Value" => $preferred_store);
			}
			$jobinterest = ($this->getRequest()->getPost('jobinterest')==1)?"Yes":"No";
			if ($jobinterest)
			{
				$customFields[] = array("Key" => "JobInterest", "Value" => $jobinterest);
			}
			
			// Campaign Monitor API Credentials
			$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
            $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
			
			$customFields[] = array("Key" => "securehash", "Value" => md5($email.$apiKey));

			if($apiKey && $listID) 
			{
                try 
				{
                    $client = new CS_REST_Subscribers($listID,$apiKey);
                } catch(Exception $e) {
                    Mage::helper('campaignmonitor')->log("Error connecting to CampaignMonitor server: ".$e->getMessage());
                    $session->addException($e, $this->__('There was a problem with the subscription'));
                    $this->_redirectReferer();
                }
				//Mage::helper('campaignmonitor')->log(print_r($customFields,true));
				try 
				{    
					$result = $client->add(
								array(
									"EmailAddress" => $email,
									"Name" => $name,
									"CustomFields" => $customFields,
									"Resubscribe" => true
									)
							);
				} catch(Exception $e) {
					Mage::helper('campaignmonitor')->log("Error in CampaignMonitor REST call: ".$e->getMessage());
					$session->addException($e, $this->__('There was a problem with the subscription'));
					$this->_redirectReferer();
				}
			}
			
			// Save for Magento backend
			$jobinterest = ($this->getRequest()->getPost('jobinterest')==1)?"YES":"NO";
			
			if (isset($mobile)) $subscriber->setSubscriberMobile($mobile);
			if (isset($periodicity)) $subscriber->setSubscriberPeriodicity($periodicity);
			if (isset($mobilesubscription)) $subscriber->setSubscriberMobilesubscription($mobilesubscription);
			if (isset($state)) $subscriber->setSubscriberState($state);
			if (isset($postcode)) $subscriber->setSubscriberPostcode($postcode);
			if (isset($preferred_store)) $subscriber->setSubscriberPreferredStore($preferred_store);
			
			$subscriber
				->setSubscriberJobinterest($jobinterest)
				->save();
			
            if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been saved.'));
            } else {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been removed.'));
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
