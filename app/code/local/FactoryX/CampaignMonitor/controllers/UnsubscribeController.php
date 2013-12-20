<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_subscribers.php";
class FactoryX_CampaignMonitor_UnsubscribeController extends Mage_Core_Controller_Front_Action
{	
    public function indexAction()
    {
        // Don't do anything if we didn't get the email parameter
        if(isset($_GET['email']))
        {
            $email = $_GET['email'];
			
			// Get the CampaignMonitor credentials
            $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
            $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
			
            $redirectUrl = trim(Mage::getStoreConfig('newsletter/campaignmonitor/redirect_url'));
            
            // Check that the email address actually is unsubscribed in Campaign Monitor.
            if($apiKey && $listID)
            {
				// Retrieve the subscriber
                try 
				{
					$client = new CS_REST_Subscribers($listID,$apiKey);
					$result = $client->get($email);
                } catch (Exception $e) {
                    Mage::helper('campaignmonitor')->log(sprintf("%s Error in SOAP call: %s", __METHOD__, $e->getMessage()));
                    $session->addException($e, $this->__('There was a problem with the unsubscription'));
                    $this->_redirectReferer();
                }

				// Get the subscription state
                $state = "";
                try
				{
					if($result->was_successful() && isset($result->response->State)) 
					{
						$state = $result->response->State;
					}
				} catch(Exception $e) {
					Mage::helper('campaignmonitor')->log(sprintf("%s Error in SOAP call: %s", __METHOD__, $e->getMessage()));
                    $session->addException($e, $this->__('There was a problem with the unsubscription'));
                    $this->_redirectReferer();
				}
                
                // If we are unsubscribed, deleted or not subscribed in Campaign Monitor, mark us as
                // unsubscribed in Magento.
                if ($state != "Unsubscribed" && $state != "Not Subscribed" && $state != "Deleted")
                {
					try
					{
						$result = $client->unsubscribe($email);
						
						if($result->was_successful()) 
						{
							$collection = Mage::getModel('newsletter/subscriber')
									->loadByEmail($email)
									->unsubscribe();
							Mage::getSingleton('customer/session')->addSuccess($this->__('You were successfully unsubscribed'));
						}
					}
					catch (Exception $e)
                    {
                        Mage::helper('campaignmonitor')->log(sprintf("%s %s", __METHOD__, $e->getMessage()));
                        Mage::getSingleton('customer/session')->addError($this->__('There was an error while saving your subscription details'));
                    }
                }
                elseif($state == "Unsubscribed" || $state == "Not Subscribed" || $state == "Deleted")
                {
					try
					{
						$subscriberStatus = Mage::getModel('newsletter/subscriber')
										->loadByEmail($email)
										->getStatus();
						// 2 = Not Activated
						// 1 = Subscribed
						// 3 = Unsubscribed
						// 4 = Unconfirmed
						if ($subscriberStatus != 3)
						{
							$unsubscribe = Mage::getModel('newsletter/subscriber')
										->loadByEmail($email)
										->unsubscribe();
							Mage::getSingleton('customer/session')->addSuccess($this->__('You were successfully unsubscribed'));
							
							$block = Mage::getModel('cms/block')->load('unsubscribe-custom-message');

							if ($block) 
							{
								Mage::getSingleton('customer/session')->addNotice($block->getContent());
							}
						}
						else
						{
							Mage::getSingleton('customer/session')->addSuccess($this->__('You have already unsubscribed to our newsletter, click <a href="/subscribe">here</a> to resubscribe'));
						}
					} catch (Exception $e) {
                        Mage::helper('campaignmonitor')->log(sprintf("%s %s", __METHOD__, $e->getMessage()));
                        Mage::getSingleton('customer/session')->addError($this->__('There was an error while saving your subscription details'));
                    }
                }
            }
        }
        if (!$redirectUrl) 
		{
	        $redirectUrl = "/customer/account/";
        }
        
        $this->_redirectUrl($redirectUrl);        
    }
}
?>
