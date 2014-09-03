<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_subscribers.php";

class FactoryX_CampaignMonitor_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    //
    //  Subscribe the user to Magento 
    //     $params : all the content from the form, add element into the mapping to map them with database
    //               empty means just ignore
    //              $first - using for creating the coupon code for first time subscriber
    //     $confirmEmail : are we sending email to the subscriber for successful subscription?
    //
    public function subscribeWithDetails($params,$confirmEmail = true, $sync = false, $update = false, $first = false){   
		try {
			// load email (incase this is existing subscriber that unsubscribed)
			$email = $params['email'];
			if (!$email) return;
			$this->loadByEmail($params['email']);
			//if (!$this->getId()) return;


			// Is this an unsubscription :(
			if (isset($params['unsubscribe']) && $params['unsubscribe'] == 1){
				$this->setStatus(self::STATUS_UNSUBSCRIBED);
				$this->save();            
				return true;
			}

			//$customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
			//if ($customer) {$customer->setIsSubscribed(true);$customer->setData('website_id',$this->getStoreId());$customer->save();}
			// Just an update? Easy peasy 
			
			if ($update){
				foreach ($params as $key => $value){
					$this->setData($key,$value);
				}            
				$this->save();                     
				return true;
			}
			
			// Get API key for secure hash comparison in order to decide if we need to return information
			$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
			$listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));     
			$isUpdate = false;
			
			if ($sync){
				// mapping between CM and MAGENTO subscriber model          
				$mapping = $this->generateMapping('campaignmonitor','subscriber');            
				$params = $this->getCMData($email);
			}else{
				// mapping between FORM and MAGENTO subscriber model
				$mapping = $this->generateMapping('formfields','subscriber');
				// for some reason the DOB is not playing nice from the form, so we clean them
				if (array_key_exists('dob',$params) && $params['dob']){
					$date = new Zend_Date($params['dob'], 'd/M/Y');
					//$date = DateTime::createFromFormat('d/m/Y', $params['dob']);
					$params['dob'] = $date->toString('M/d/Y');      
				} 
			}

			// map mobile to yes if value = 1
			if ((isset($params['is_subscribed_sms']) && $params['is_subscribed_sms'] == 1) || !empty($params['mobile']) || !empty($params['Mobile'])){$params['is_subscribed_sms'] = 'YES';}

			// set the data from form to model
			foreach ($params as $key => $param){
				if (!isset($mapping[$key])){
					// this is some information that we won't know how to store in magento, so we log
					Mage::helper('campaignmonitor')->log("FactoryX_CampaignMonitor_Model_Subscriber: ".$key." is not defined in magento subscriber mapping.");
				}elseif (!empty($param) && !empty($mapping[$key])){
					$this->setData($mapping[$key],$param);
				}
			}      

			// Set securehash
			$this->setData('subscriber_securehash',md5($email.$apiKey));          
			// Set subscription date
			$this->setData('subscriber_subscriptiondate',date("Y-m-d H:i:s"));

			// Coupon stuff
			$helper = Mage::helper('campaignmonitor');
			if ((!$sync || $first) && $helper->isCouponEnabled() && $confirmEmail) {
				//Mage::helper('campaignmonitor')->log(sprintf("%s->isCouponEnabled=%s", __METHOD__, $helper->isCouponEnabled()) );
				$couponcode = $this->generatePromocode(
					$email,
					$helper->getCouponMinSpend(),
					$helper->getCouponValue(),
					$helper->getCouponOffer(),
					$helper->getCouponPrefix(),
					$helper->getCouponValidity()
				);
				//Mage::helper('campaignmonitor')->log(sprintf("%s->couponcode=%s", __METHOD__, $couponcode) );                
				if ($couponcode) {
					$this->setSubscriberCoupon($couponcode);
				}
			}

			// set subscriber status to subscribed
			$this->setStatus(self::STATUS_SUBSCRIBED);
			$this->setIsStatusChanged(true);

			// check if the customer is logged in and the email is matched
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				$customer = Mage::getSingleton('customer/session');
				$customerData = Mage::getModel('customer/customer')->load($customer->getId())->getData();
				if ($email == $customerData['email']){
					$this->setCustomerId($customerData['entity_id']);
				}
			}
			
			// set store
			$this->setStoreId(Mage::app()->getStore()->getId());

            $this->save();
            if ($confirmEmail || $first) {              
                $this->sendConfirmationSuccessEmail();
            }
            return $this->getStatus();
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
            return false;
        } 
        return true;       
    }

    /*
    * Subscriber subscriber to CM 
    * @params: array of data to pass into CM
    * @sync: sync from Magento, hence need to change the mapping
    */
    public function subscribeWithDetailsCM($params, $sync = false, $update = false){ 
        // Get configurations
        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));

        // Is this an unsubscription :(
        if (isset($params['unsubscribe']) && $params['unsubscribe'] == 1){
            $client = new CS_REST_Subscribers($listID,$apiKey);
            $client->unsubscribe($params['email']);   
            return true;
        }    

        if ($update){ 
            
            // is there a new email?
            $email = $params['email']; unset($params['email']);
            $new_email = null;
            if (isset($params['new_email'])) { $new_email = $params['new_email']; unset($params['new_email']);} else {$new_email=$email;}
            $length = sizeof($params);
            $firstname='';$lastname='';

            try{
                $client = new CS_REST_Subscribers($listID,$apiKey);
                $result = $client->update($email,array(
                                                    'CustomFields'=>$params,
                                                    'EmailAddress'=>$new_email,
                                                    'Name'=>$firstname.' '.$lastname,
                                                    'Resubscribe'=>true));
            } catch (Exception $e) {
                Mage::helper('campaignmonitor')->log("Error in CampaignMonitor REST call: ".$e->getMessage());
                return false;
            }
            return true;       
        }

        $prefix = '';
        if ($sync) {
            $email = $params['email'];
            // mapping between Magento subscriber and CM
            $mapping = $this->generateMapping('subscriber','campaignmonitor');

            $this->loadByEmail($email);
            if (!$this->getId()) return;

            $customFields = array();

            // Setting secure hash just to be safe
            $securehash = $this->getData('subscriber_securehash');
            if (empty($securehash)){
                $this->setData('subscriber_securehash',md5($email.$apiKey));
                $this->save();
            }
            $params = $this->getData();
            $prefix = 'subscriber_';

        }else{
            // mapping between FORM and CM
            $mapping = $this->generateMapping('formfields','campaignmonitor');
        }
        $customFields = array();       

		// Handle mobile mini subscription name
		if (array_key_exists('mini',$params) && $params['mini'] && !array_key_exists('firstname',$params) && !array_key_exists('lastname',$params))
		{
			$name = "(Guest)";
		}
		else
		{
			$name = $params[$prefix.'firstname'].' '.$params[$prefix.'lastname'];
		}

        // Add popup source
        if (array_key_exists('popup',$params) && $params['popup']){
            $customFields[] = array("Key" => "Source", "Value" => "popup");
        }

        // set the data from form to model
        foreach ($params as $key => $param){
            if (!isset($mapping[$key])){
                // this is some information that we won't know how to store in cm, so we log
                Mage::helper('campaignmonitor')->log("FactoryX_CampaignMonitor_Model_Subscriber: ".$key." is not defined in the campaign monitor mapping.");
            }elseif (!empty($param) && !empty($mapping[$key])){
                $customFields[] = array("Key"=>$mapping[$key],"Value"=>$param);
            }
        }  
        if (!$sync){
            // add secure hash
            $customFields[] = array("Key"=>"securehash","Value"=>md5($params['email'].$apiKey));
        }
        // ok let's add
        try{       
            Mage::helper('campaignmonitor')->log($params);     
            $client = new CS_REST_Subscribers($listID,$apiKey);
            $result = $client->add(array(
                                        "EmailAddress" => $params[$prefix.'email'],
                                        "Name" => $name,
                                        "CustomFields" => $customFields,
                                        "Resubscribe" => true  // if the subscriber is already unsubscried - subscribe again!
                                        ));
        } catch (Exception $e) {
            Mage::helper('campaignmonitor')->log("Error in CampaignMonitor REST call: ".$e->getMessage());
            $session->addException($e, $this->__('There was a problem with the subscription'));
            return false;
        }       
        return true;
    }    

    public function syncSubscriber($email,$first = false){        
        // Get API key 
        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));

        // Avaliable?
        $magentoAvailable = false;
        $cmAvailable = false; $cmData = array();

        // get subscriber from Magento
        $subscriberModel = $this->loadByEmail($email);
        if ($subscriberModel && $subscriberModel->getStatus() == self::STATUS_SUBSCRIBED) $magentoAvailable = true;

        // get subscribder from CM
        $cmModel = $this->getCMData($email); 
        if (!is_null($cmModel)){
            $cmAvailable = true;
        }

        // if missing from CM, we sync from Magento
        if (!$cmAvailable){
            $this->subscribeWithDetailsCM(array('email'=>$email),true);
            return;
        }

        // if missing from Magento, we sync from CM
        if (!$magentoAvailable){
            $this->subscribeWithDetails(array('email'=>$email),false,true,false,$first);
            return;
        }

        // mapping between CM and MAGENTO
        $mapping = $this->generateMapping('campaignmonitor','subscriber');
        $mage_array = array(); $cm_array = array(); 
        
        foreach($mapping as $cm => $mage){
            if (array_key_exists($cm,$cmModel)){
                $this->syncValue($this->getData($mage),$cmModel[$cm],$mage,$cm,$mage_array,$cm_array);
            }
            //echo $this->getData($prefix.$mapping[$key])." ".$cmModel[$key]." <br/>";            
        }

        // Do we need to update CM?
        if (!empty($cm_array)){
            //var_dump($cm_array);
            $cm_array['email'] = $email;
            $this->subscribeWithDetailsCM($cm_array,false,true);
        }
        
        // Do we need to update Mage?
        if (!empty($mage_array)){            
            $mage_array['email'] = $email;
            $this->subscribeWithDetails($mage_array,false,false,true);
        }

        

        return;

    }


    /*
    *  Select which value to sync
    *  @mage_val: Magento value
    *  @cm_val: CM value
    *  @mage_key: Magento data name
    *  @cm_key: CM fields name
    *  @mage_array: array to add in if there is new information for mage
    *  @cm_array: array to add in if there is new information for CM
    *  Always treat the CM as master
    */
    private function syncValue($mage_val, $cm_val, $mage_key, $cm_key, &$mage_array, &$cm_array){
        // cases that we should not need to sync value
        if ((empty($mage_val) && empty($cm_val)) || ($mage_val == $cm_val)) return;
        Mage::helper('campaignmonitor')->log('update happens: '.$mage_val.' '.$cm_val);
        if (empty($cm_val)){
            $cm_array[] = array("Key" => $cm_key, "Value" => $mage_val);            
        }else{            
            $mage_array[$mage_key] = $cm_val;
        }
        return;
    }

    /*
    *  Get information from CM
    *
    */
    public function getCMData($email){
        // Get configurations
        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
        try 
        {
            $client = new CS_REST_Subscribers($listID,$apiKey);
            $result = $client->get($email);            
            if ($result->http_status_code == 200 && $result->response->State == 'Active') //200 means there is some details coming back
            { 
                $response['email'] = $result->response->EmailAddress;
                if (strpos($result->response->Name,(" ")) > 0)
				{
					list($fname, $lname) = explode(' ',$result->response->Name);
					$response['firstname'] = $fname;
					$response['lastname']  = $lname;        
				}
				else
				{
					$response['firstname'] = $result->response->Name;
					$response['lastname']  = "";
				}        
                $response['Date'] = $result->response->Date;
                $response['Date']= substr($response['Date'],0,10);
                // We store the details in the response array
                foreach ($result->response->CustomFields as $subscriberDetail)
                {
                    $response[str_replace(' ','',$subscriberDetail->Key)] = $subscriberDetail->Value;
                }
                return $response;
            }                   
        } catch(Exception $e) {
            Mage::helper('campaignmonitor')->log($e->getMessage());            
        }
        return null;         
    }

    public function generateMapping($source,$destination){
        $result = array();
        $mappings = $linkedAttributes = @unserialize(Mage::getStoreConfig('newsletter/campaignmonitor/m_to_cm_attributes',
                Mage::app()->getStore()->getStoreId()));
        foreach($mappings as $mapping){
            if (!empty($mapping[$source]) && !empty($mapping[$destination])){
                $result[$mapping[$source]] = $mapping[$destination];
            }
        }
        return $result;
    }

    /**
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer)
    {
        $this->loadByCustomer($customer);

        if ($customer->getImportMode()) {
            $this->setImportMode(true);
        }

        if (!$customer->getIsSubscribed() && !$this->getId()) {
            // If subscription flag not set or customer is not a subscriber
            // and no subscribe below
            return $this;
        }

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

       /*
        * Logical mismatch between customer registration confirmation code and customer password confirmation
        */
       $confirmation = null;
       if ($customer->isConfirmationRequired() && ($customer->getConfirmation() != $customer->getPassword())) {
           $confirmation = $customer->getConfirmation();
       }

        $sendInformationEmail = false;
        if ($customer->hasIsSubscribed()) {
            $status = $customer->getIsSubscribed()
                ? (!is_null($confirmation) ? self::STATUS_UNCONFIRMED : self::STATUS_SUBSCRIBED)
                : self::STATUS_UNSUBSCRIBED;
            /**
             * If subscription status has been changed then send email to the customer
             */
            if ($status != self::STATUS_UNCONFIRMED && $status != $this->getStatus()) {
                $sendInformationEmail = true;
            }
        } elseif (($this->getStatus() == self::STATUS_UNCONFIRMED) && (is_null($confirmation))) {
            $status = self::STATUS_SUBSCRIBED;
            $sendInformationEmail = true;
        } else {
            $status = ($this->getStatus() == self::STATUS_NOT_ACTIVE ? self::STATUS_UNSUBSCRIBED : $this->getStatus());
        }

        if($status != $this->getStatus()) {
            $this->setIsStatusChanged(true);
        }

        $this->setStatus($status);

        if(!$this->getId()) {
            $storeId = $customer->getStoreId();
            if ($customer->getStoreId() == 0) {
                $storeId = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
            }
            $this->setStoreId($storeId)
                ->setCustomerId($customer->getId())
                ->setEmail($customer->getEmail());
        } else {
            $this->setStoreId($customer->getStoreId())
                ->setEmail($customer->getEmail());
        }

        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));

        // Set securehash
        $this->setData('subscriber_securehash',md5($customer->getEmail().$apiKey));          
        // Set subscription date
        $this->setData('subscriber_subscriptiondate',date("Y-m-d H:i:s"));

        // Coupon stuff
        $helper = Mage::helper('campaignmonitor');
        if ($helper->isCouponEnabled() && !$this->getSubscriberCoupon()) {
            //Mage::helper('campaignmonitor')->log(sprintf("%s->isCouponEnabled=%s", __METHOD__, $helper->isCouponEnabled()) );
            $couponcode = $this->generatePromocode(
                $customer->getEmail(),
                $helper->getCouponMinSpend(),
                $helper->getCouponValue(),
                $helper->getCouponOffer(),
                $helper->getCouponPrefix(),
                $helper->getCouponValidity()
            );
            //Mage::helper('campaignmonitor')->log(sprintf("%s->couponcode=%s", __METHOD__, $couponcode) );                
            if ($couponcode) {
                $this->setSubscriberCoupon($couponcode);
            }
        }
        $this->save();
        $sendSubscription = $customer->getData('sendSubscription') || $sendInformationEmail;
        if (is_null($sendSubscription) xor $sendSubscription) {
            if ($this->getIsStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                $this->sendUnsubscriptionEmail();
            } elseif ($this->getIsStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
                $this->sendConfirmationSuccessEmail();
            }
        }
        return $this;
    }

     /*
     * Create new coupon code
     * @param     string     $text 
     * @param    int     $spend
     * @param    int     $value 
     * @param   text    $offer (by_percent | by_fixed)
     * @param    string     $prefix
     * @param     int        $validdays
     * @return     Mage_SalesRule_Model_Rule
     */
    public function generatePromocode($text = "Unknown", $spend, $value, $offer, $prefix, $validdays) 
    {
        // Name of the rule
        $name = sprintf("newsletter subscription coupon for %s", $text);
        $description = $name;

        // Validity for two weeks
        $ts = Mage::getModel('core/date')->timestamp(time());
        $fromDate = date('Y-m-d', $ts);
        
        // Add two weeks
        $toDate = date('Y-m-d', strtotime(sprintf("%s +%d days", date("Y-m-d", $ts), $validdays)) );
        
        $description .= sprintf(" valid [%s - %s]", date("Y-m-d H:i:s", $ts), date('Y-m-d H:i:s', strtotime(sprintf("%s +%d days", date("Y-m-d", $ts), $validdays)) ));
        
        // Generate the hash code for the coupon
        $coupon_code = sprintf("%s%s", $prefix, $this->generateHashCode());
        
        /**
        to work out actions & conditions see Mage_Salesrule_Model_Rule::loadPost()
        */
        $conditions = array(
            "1" => array(
                "type"          => "salesrule/rule_condition_combine",
                "aggregator"    => "all",
                "value"         => "1",
                "new_child"     => false
            ),
            "1--1" => array(
                "type"          => "salesrule/rule_condition_address",
                "attribute"     => "base_subtotal",
                "operator"      => ">=",
                "value"         => $spend
            )
        );
        $actions = array(
            "1" => array(
                "type"          => "salesrule/rule_condition_product_combine",
                "aggregator"    => "all",
                "value"         => 1,
                "new_child"     => false
            )
        );
        
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
            ->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
            ->setIsActive(1)
            ->setData('conditions', $conditions)
            ->setData('actions', $actions)            
            //->setConditionsSerialized('a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
            //->setActionsSerialized('a:6:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
            ->setStopRulesProcessing(0)
            ->setIsAdvanced(0)
            ->setProductIds('')
            ->setSortOrder(5) // Priority
            ->setSimpleAction($offer)
            ->setDiscountAmount($value)
            ->setDiscountQty(null)
            ->setDiscountStep('0')
            ->setSimpleFreeShipping('0')
            ->setApplyToShipping('0')
            ->setIsRss(0)
            ->setWebsiteIds(array(1))
            ->setStopRulesProcessing(1) // 0 = no, 1 = yes
            ->setStoreLabels(array(sprintf("Welcome %s%s OFF", ($offer == Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION?"%":"$"), $value)));

        /*
        $coupon->setData('conditions', $conditions)
        $coupon->setData('actions', $actions)
        */
        $coupon->loadPost($coupon->getData());
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

        /**
     * Retrieve all customer attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        // fetch write database connection that is used in Mage_Core module
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        // now $write is an instance of Zend_Db_Adapter_Abstract
        $result = $write->query("select distinct column_name from information_schema.columns where table_name = 'newsletter_subscriber' order by ordinal_position");
        $array = array();
        while ($row = $result->fetch()) {
            $array[] = $row['column_name'];
        }
        return $array;
    }

}