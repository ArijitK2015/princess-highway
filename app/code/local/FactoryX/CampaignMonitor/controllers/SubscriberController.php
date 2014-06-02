<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_subscribers.php";
include_once Mage::getModuleDir('controllers','Mage_Newsletter').DS."SubscriberController.php";

class FactoryX_CampaignMonitor_SubscriberController extends Mage_Newsletter_SubscriberController
{
    /* 
		To be used in the front end via AJAX call
		or be used within this class with parameter $email

		Return a response array with status and message
    */
    public function retrieveAction($email = null)
    {   //$mapping = $linkedAttributes = @unserialize(Mage::getStoreConfig('newsletter/campaignmonitor/m_to_cm_attributes',
        //        Mage::app()->getStore()->getStoreId()));print_r($mapping);return;

        // Parameters given in AJAX via GET
        $params = $this->getRequest()->getParams();
        
        // Initial response array
        $response = array();

        // Get the sessions
        $customerSession = Mage::getSingleton('customer/session');
        $session   = Mage::getSingleton('core/session');
        
        // If the email is provided, we get it
        if (array_key_exists('email',$params)){ 
        	$email = $params['email'];
        }
        
        // If the securehash is provided, we get it
        if (array_key_exists('securehash',$params)){ 
        	$securehash = $params['securehash'];
        }

        // Get API key for secure hash comparison in order to decide if we need to return information
        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));

        // If the email is not valid, return the response and exit now
        if (!Zend_Validate::is($email, 'EmailAddress')) {
        	$this->putResponse("ERROR","Please enter a valid email address.");            
            return;
        }

        // Setup status
        // 0 - not found
        // 1 - unsubscribed
        // 2 - subscribed
        $magentoStatus=0;$cmStatus=0;

        // Get this subscriber from Magento
        $subscriberModel = Mage::getModel('campaignmonitor/subscriber')->loadByEmail($email);
        if ($subscriberModel){
        	if ($subscriberModel->isSubscribed()){
        		$magentoStatus=2;
        	}else{
        		$magentoStatus=1;
        	}
        }

        // Get this subscriber from Campaign Monitor
        $cmStatus=$this->getCMStatus($email);
		if (is_null($magentoStatus) || is_null($cmStatus)){
			// something went terribly wrong
			$this->putResponse("ERROR","We cannot process your subscription at the moment, please try again later.");
		}elseif ($magentoStatus == 2 || $cmStatus == 2){

            // ok let's sync them up!
            $subscriberModel->syncSubscriber($email);

			// if () there is secured hash then we need to get information
			if (isset($securehash) && $securehash == md5($email.$apiKey)){     
                // If there is unsub, we update first
                Mage::log($params);
                if (array_key_exists('unsub',$params)){
                    $subscriberModel->unsubInterest($email,$params['unsub']);                    
                }                
                $response = $subscriberModel->getCMData($email);           
				$response['status'] = "EXISTING";                            
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));                    
			}else{
				// if the subscriber appears in any of those source, we shouldn't appear to re-subscribe
				$this->putResponse("ERROR","You have already subscribed. Please visit the link in our newsletter to update your details.");
			}
		}else{
			$this->putResponse("NEW","You have not subscribed to our newsletter yet, you can fill the form below to do so.");
			return "NEW";
		}
    	return;
    }

    public function newAction(){    	

        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));

    	// get all the parameters
    	$params = $this->getRequest()->getParams();

        // get session
        $session = Mage::getSingleton('core/session');

    	$isNew = $this->retrieveAction($params['email']) == "NEW";
		
		// no welcome email
		if (array_key_exists('welcome',$params))
		{
			$welcome = false;
		}
		else $welcome = true;
		

        // subscriber model
        $subscriberModel = Mage::getModel('campaignmonitor/subscriber');

        // Popup subscription
        if ($this->getRequest()->getPost('popup')) {
            $popup = true;
            $params['popup'] = true;
        }else{
            $popup = false;
        }
		
		// fullname handler
		if(array_key_exists('name',$params) && !array_key_exists('firstname',$params) && !array_key_exists('lastname',$params))
		{
			if (strpos($params['name'],(" ")) > 0)
			{
				$params['firstname'] = substr($params['name'],0,strpos($params['name'],(" ")));
				$params['lastname'] = substr($params['name'],strpos($params['name'],(" "))+1);
			}
			else
			{
				$params['firstname'] = $params['name'];
				$params['lastname'] = "";
			}
		}

    	// is this new subscriber?
    	if ($isNew) {
    		
			// new subscriber
			if ($subscriberModel->subscribeWithDetails($params,$welcome) && $subscriberModel->subscribeWithDetailsCM($params)){
                if (!$popup){
                    $session->addSuccess($this->__('Thank you for your subscription.'));
                }
            }else{
                if (!$popup){
                    $session->addException($e, $this->__('There was a problem with the subscription.'));
                }
            }
    	}else{
            // ok this is updating information
            if ($subscriberModel->subscribeWithDetails($params,false) && $subscriberModel->subscribeWithDetailsCM($params)){
                if (array_key_exists('unsubscribe',$params) && $params['unsubscribe'] == 1){                    
                        $session->addSuccess($this->__('Your has been unsubscribed.'));                    
                }else{
                    if (!$popup){
                        $session->addSuccess($this->__('Your subscription has been updated.'));
                    }else{
                        $response = array();
                        $response["status"] = "existing";
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                        return;
                    }
                }
            }else{
                if (!$popup){
                    $session->addException($e, $this->__('There was a problem with updating the subscription.'));
                }
            }
        }		

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('popup')) {
            $response = array();
            $response["status"] = "subscribed";
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }


		// Redirect to the right URL
        $url = sprintf("%ssubscribe", Mage::getBaseUrl());        
        if (isset($params['mini']) && isset($params['mini']) == 1 && $isNew)
        {
          $url.='?email='.$params['email'].'&key='.md5($params['email'].$apiKey);        
        }
        $this->_redirectUrl($url);
    }

    /* get subscriber status in CM
    *  parameter - subscriber email
    *  return null if there is a problem
    * 	      0 for not found
    *         1 for unsubscribed
    *         2 for subscribed
    */
    private function getCMStatus($email){
    	$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
    	$listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
    	try 
        {
            $client = new CS_REST_Subscribers($listID,$apiKey);
            $result = $client->get($email);            
            if ($result->http_status_code == 200) //200 means there is some details coming back
            {
            	if ($result->response->State == 'Active'){
            		return 2;	
            	}else{
            		return 1;
            	}            	
            }elseif ($result->http_status_code == 400){ //400 means not found
            	return 0;
            }
            return null;

        } catch(Exception $e) {
            Mage::helper('campaignmonitor')->log($e->getMessage());            
            return null;
        }
    }

    // Time to give response
    private function putResponse($status,$message){
    	$response = array();
    	$response['status'] = $status;
        $response['message'] = $message;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

}