<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_subscribers.php";
include_once Mage::getModuleDir('controllers','Mage_Newsletter').DS."SubscriberController.php";

class FactoryX_CampaignMonitor_SubscriberController extends Mage_Newsletter_SubscriberController
{

	/**
	 * Retrieve email associated details action
	 */
	public function retrieveAction()
	{
		// Parameters given in AJAX via GET
		$params = $this->getRequest()->getParams();
		// Response array
		$response = array();
		
		// Get the sessions
        $customerSession = Mage::getSingleton('customer/session');
		$session   = Mage::getSingleton('core/session');
		
		// Get the email
		$email = $params['email'];
		// If the securehash is provided, we get it
		if (array_key_exists('securehash',$params)) $securehash = $params['securehash'];

		// Test is email address given is valid
		if (!Zend_Validate::is($email, 'EmailAddress')) {
			$response['status'] = "ERROR";
			$response['message'] = "Please enter a valid email address.";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}

		// Test if subscription is possible for guest
		if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
			!$customerSession->isLoggedIn()) {
			$response['status'] = "ERROR";
			$response['message'] = $this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl());
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		
		// Retrieve details from Campaign Monitor
		$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
		$listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));

		if($apiKey && $listID && $email) 
		{
			// Create the REST Client
			try 
			{
				$client = new CS_REST_Subscribers($listID,$apiKey);
			} catch(Exception $e) {
				Mage::helper('campaignmonitor')->log(__METHOD__ . "Error connecting to CampaignMonitor server: ".$e->getMessage());
				$response['status'] = "ERROR";
				$response['message'] = "There was a problem with the subscription.";
				return;
			}
			// Retrieve details
			try 
			{
				$result = $client->get($email);
				//Mage::helper('campaignmonitor')->log(print_r($result,true));
				//Mage::helper('campaignmonitor')->log(print_r($result->response->CustomFields,true));						

				// Can we connect to the server?				
				if ($result->http_status_code == 0){
					$response['status'] = "ERROR";
					$response['message'] = "We cannot process your subscription at the moment, please try again later.";
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return;
				}

				// If existing and active subscriber
				if ($result->was_successful() && $result->response->State == 'Active')
				{
					// If the secure hash is not provided, the subscriber can't update his/her details
					if (!isset($securehash))
					{
						$response['status'] = "ERROR";
						$response['message'] = "You have already subscribed. Please visit the link in our newsletter to update your details.";
						$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
						return;
					}

					// If the secure hash doesn't match the expected hash, that's invalid
					if ($securehash != md5($email.$apiKey))
					{
						$response['status'] = "INVALID";
						$response['message'] = Mage::getBaseUrl()."subscription_invalid";
						$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
						return;
					}

					// Get the name
					$response['Name'] = $result->response->Name;

					// We store the details in the response array
					foreach ($result->response->CustomFields as $subscriberDetail)
					{
						$response[$subscriberDetail->Key] = $subscriberDetail->Value;
					}
					$response['status'] = "EXISTING";
					
					// If has just been subscribed via mini, we change the message
					if ($session->getMini())
					{
						$response['message'] = "Thanks for subscribing to our newsletter, you can update your details using the form below.";
					}
					else $response['message'] = "You have already subscribed to our newsletter, you can update your details using the form below.";
				}
				else
				{
					// New subscriber
					$response['status'] = "NEW";
					$response['message'] = "You have not subscribed to our newsletter yet, you can fill the form below to do so.";
				}
				//Mage::helper('campaignmonitor')->log(print_r($response,true));
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;
			} catch(Exception $e) {
				Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in CampaignMonitor REST call: ".$e->getMessage());
				$response['status'] = "ERROR";
				$response['message'] = "There was a problem with the subscription.";
				return;
			}
		}
		else
		{
			Mage::helper('campaignmonitor')->log(__METHOD__ . "Missing API Key or API List or email");
			$response['status'] = "ERROR";
			$response['message'] = "There was a problem with the subscription.";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
	}
    /**
      * New subscription action
      */
    public function newAction()
    {
		// Get the sessions
		$session   = Mage::getSingleton('core/session');
		$customerSession = Mage::getSingleton('customer/session');
		
		if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) 
		{
			// Variables 
			$firstname = "";
			$lastname = "";
			$mobile = "";
			$state = "";
			$postcode = "";
			$jobinterest = "";
			$periodicity = "";
			$dob = "";
			
			// Popup subscription
			if ($this->getRequest()->getPost('popup')) {
				$popup = true;
			}else{
				$popup = false;
			}

			// Mini footer subscription
			$mini = $this->getRequest()->getPost('mini');
			$session->setMini($mini);
			
			// Email handler
            $email     = (string)$this->getRequest()->getPost('email');
			$session->setEmail($email);
			
			// Name handler
			if ($this->getRequest()->getPost('name')) 
			{
				$name = (string)$this->getRequest()->getPost('name');
				$name = trim($name);
				// Split the name to get the first and last names
				$names = explode(" ", $name, 2);
				if (!empty($names) && isset($names[0]) && isset($names[1])) 
				{
					$firstname = (string) $names[0];
					$lastname = (string) $names[1];
				}
				else 
				{
					$firstname = $name;
					$lastname = "";
				}
				$session->setName($name);
				$customFields[] = array("Key" => "fullname", "Value" => $name);
			}

			// New email handler
			$new_email = $email;
			if ($this->getRequest()->getPost('new_email'))
			{
				$new_email = $this->getRequest()->getPost('new_email');
			}
			
			// Exception if customer logged in
			$customerHelper = Mage::helper('customer');
			if($customerHelper->isLoggedIn()) 
			{
				$customer = $customerHelper->getCustomer();
				$name = $customer->getFirstname() . " " . $customer->getLastname();
				$customFields = FactoryX_CampaignMonitor_Model_Customer_Observer::generateCustomFields($customer);
			}
			
			// Code to handle the extra fields
			if ($this->getRequest()->getPost('mobile'))
			{
				$mobile = (string) $this->getRequest()->getPost('mobile');
				$mobile = str_replace(" ", "", $mobile);
				$session->setMobile($mobile);
				$customFields[] = array("Key" => "Mobile", "Value" => $mobile);
			}
      
			if ($this->getRequest()->getPost('state'))
			{
				$state = (string) $this->getRequest()->getPost('state');
				$session->setState($state);
				$customFields[] = array("Key" => "State", "Value" => $state);
			}
			
			if ($this->getRequest()->getPost('postcode'))
			{
				$postcode = (string) $this->getRequest()->getPost('postcode');
				$session->setPostcode($postcode);
				$customFields[] = array("Key" => "Postcode", "Value" => $postcode);
			}
			
			if ($this->getRequest()->getPost('periodicity') != "-1")
			{
				$periodicity = (string) $this->getRequest()->getPost('periodicity');
				$session->setPeriodicity($periodicity);
				$customFields[] = array("Key" => "Periodicity", "Value" => $periodicity);
			}
			
			if ($this->getRequest()->getPost('month') && $this->getRequest()->getPost('day')) // && $this->getRequest()->getPost('year'))
			{
				$dob_m = (string) $this->getRequest()->getPost('month');
				$session->setMonth((string)$this->getRequest()->getPost('month'));
				$dob_d = (string) $this->getRequest()->getPost('day');
				$session->setDay((string)$this->getRequest()->getPost('day'));
				//$dob_y = (string) $this->getRequest()->getPost('year');
				if ($dob_m != -1 && $dob_d != -1) // && $dob_y != -1) 
				{
					$dob = date("Y-m-d", mktime(0, 0, 0, $dob_m, $dob_d, date("Y")));
				}
				else $dob = "";
				// Update the DateOfBirth which is retrieved via the generateCustomFields function
				$found = false;
				for ($i = 0; $i < count($customFields); $i++)
				{
					if ($customFields[$i]["Key"] == "DateOfBirth")
					{
						$customFields[$i] = array("Key" => "DateOfBirth", "Value" => $dob);
						$found = true;
					}
				}
				if (!$found) $customFields[] = array("Key" => "DateOfBirth", "Value" => $dob);
			}
			
			if ($this->getRequest()->getPost('jobinterest'))
			{
				$session->setJobinterest((string)$this->getRequest()->getPost('jobinterest'));
				$customFields[] = array("Key" => "JobInterest", "Value" => (string)$this->getRequest()->getPost('jobinterest'));
				$jobinterest = ($this->getRequest()->getPost('jobinterest')=="Yes")?1:0;
			}
			
			$subscriptiondate = date("Y-m-d");

			Mage::helper('campaignmonitor')->log(__METHOD__ . "Adding newsletter subscription via frontend 'Sign up' block for $email");

			$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
			$listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));

            // Check if currently subscribed for mini-form action
			
			if($apiKey && $listID) 
			{
				// Create the REST Client
				try 
				{
					$client = new CS_REST_Subscribers($listID,$apiKey);
				} catch(Exception $e) {
					Mage::helper('campaignmonitor')->log(__METHOD__ . "Error connecting to CampaignMonitor server: ".$e->getMessage());
					$response['status'] = "ERROR";
					$response['message'] = "There was a problem with the subscription.";
					return;
				}

				// Retrieve the subscriber
				try 
				{
					$result = $client->get($email);
					$mini_subscribed = 0;
					$active_state = false;
					if ($result->was_successful() && $result->response->State == 'Active')
					{
						$mini_subscribed = 1;
						$active_state = true;
					}
				} catch(Exception $e) {
					Mage::helper('campaignmonitor')->log(__METHOD__ . "Error connecting to CampaignMonitor server: ".$e->getMessage());
					$response['status'] = "ERROR";
					$response['message'] = "There was a problem with the subscription.";
					return;
				}

				// Secure Hash
				$customFields[] = array("Key" => "securehash", "Value" => md5($new_email.$apiKey));	

				// Source for popup 
				if ($popup){
					$customFields[] = array("Key" => "Source", "Value" => "popup");						
				}

                // If a user is logged in, fill in the Campaign Monitor custom
                // attributes with the data for the logged-in user
                if($customerHelper->isLoggedIn() && false) 
				{
                    try 
					{    
                        $result = $client->add(array(
                                "EmailAddress" => $email,
                                "Name" => $name,
                                "CustomFields" => $customFields,
								"Resubscribe" => true));
                    } catch(Exception $e) {
                        Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in CampaignMonitor REST call: ".$e->getMessage());
                        $session->addException($e, $this->__('There was a problem with the subscription'));
                        $this->_redirectReferer();
                    }                     
                } 
				else 
				{
					try 
					{
						if ($this->getRequest()->getParam('hidden_hash') && $active_state)
						{
							$new_info = array(
								'EmailAddress' => $new_email,
								'Name' => $name,
								'CustomFields' => $customFields
								);
							$client->update($email,$new_info);                
						}
						else
						{
							// otherwise if nobody's logged in, ignore the custom
							// attributes and just set the name to '(Guest)'
							// Custom code if mini subscription
							if ($mini)
							{                     
								$result = $client->add(array(
										"EmailAddress" => $email,
										"Name" => "(Guest)",
										"CustomFields" => $customFields,
										"Resubscribe" => true));
							}
							// Else if subcription page
							else
							{                        
								$result = $client->add(array(
									"EmailAddress" => $email,
									"Name" => $name,
									"CustomFields" => $customFields,
									"Resubscribe" => true));
							}
							// End custom code
						}
					} catch (Exception $e) {
						Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in CampaignMonitor REST call: ".$e->getMessage());
						$session->addException($e, $this->__('There was a problem with the subscription'));
						$this->_redirectReferer();
					}
				}
            } 
			else 
			{
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Error: Campaign Monitor API key and/or list ID not set in Magento Newsletter options.");
            }
			//Mage::helper('campaignmonitor')->log(print_r($result,true));			
        }

		try 
		{
			if (!Zend_Validate::is($email, 'EmailAddress')) 
			{
				Mage::throwException($this->__('Please enter a valid email address.'));
			}

			if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
				!$customerSession->isLoggedIn()) 
			{
				Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
			}

			// Check if already subscribed
			$subscriber = Mage::getModel('campaignmonitor/subscriber')->loadByEmail($email);

			$ownerId = Mage::getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
					->loadByEmail($email)
					->getId();

			if ($subscriber && $subscriber->isSubscribed() && !$mini) 
			{
				// Subscription update
				$updateStatus = Mage::getModel('campaignmonitor/subscriber')->updateSubscription($firstname, $lastname, $email, $mobile, $state, $periodicity, $jobinterest, $dob, $postcode);					
				if ($updateStatus) 
				{
					if (!$popup){
						$session->addSuccess($this->__('Your subscription have been updated.'));
					}else{
						$response = array();
						$response["status"] = "existing";
						$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
						return;
					}
					$session->setEmail('');
				}
				else 
				{
					Mage::throwException($this->__('An error occured during the update process.'));
				}
			}
			else
			{
				// New subscriber
				$status = Mage::getModel('campaignmonitor/subscriber')->subscribeWithDetails($email, $firstname, $lastname, $mobile, $state, $periodicity, $jobinterest, $dob, $subscriptiondate, $postcode);
				
				if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) 
				{
					if (!$popup){
						$session->addSuccess($this->__('Confirmation request has been sent.'));
					}
				}
				else 
				{
					if (!$popup){
						$session->addSuccess($this->__('Thank you for your subscription.'));
					}
				}
            }
		} catch (Mage_Core_Exception $e) {
			$session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
		} catch (Exception $e) {
			$session->addException($e, $this->__('There was a problem with the subscription.'));
		}
		if ($this->getRequest()->isPost() && $this->getRequest()->getPost('popup')) {
			$response = array();
			$response["status"] = "subscribed";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		// Redirect to the right URL
        $url = sprintf("%ssubscribe", Mage::getBaseUrl());
		
        if ($mini == 1 && isset($mini_subscribed) && $mini_subscribed != 1)
		{
          $url.='?email='.$email.'&key='.md5($email.$apiKey);        
        }
        $this->_redirectUrl($url);
    }

	/**
	 *	Handle job interest subscription
	 */
    public function jobAction()
	{
		// Get the session
		$session = Mage::getSingleton('core/session');
		
		// Get the CampaignMonitor credentials
		$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
		$listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/job_list_id'));
		
		// Get the provided data
		$email    = $this->getRequest()->getParam('email');
		$state  = $this->getRequest()->getParam('state');

		try 
		{
			// Create the REST Client
			$client = new CS_REST_Subscribers($listID,$apiKey);
			// Generate the data
			$subscriber = array(
				'EmailAddress' => $email,
				'Name' => 'Guest',
				'CustomFields' => array(
					array(
						'Key'=> 'State',
						'Value' => $state
					)),
				'Resubscribe' => true
			);
			// Susbcribe
			$client->add($subscriber);
		}
		catch (Exception $e) {
			$session->addException($e, $this->__('There was a problem with the un-subscription.'));
		}
		$session->addSuccess($this->__('Thank you for register your interest.'));
		$this->_redirectReferer();
    }
}
