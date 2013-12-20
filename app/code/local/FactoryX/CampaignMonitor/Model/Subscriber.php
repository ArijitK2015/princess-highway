<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_subscribers.php";

class FactoryX_CampaignMonitor_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    /**
     * Subscribes by email with the new data
     * @todo change the other parameters into an array
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     * @param string $mobile
     * @param string $state
     * @param string $periodicity
     * @param int $jobinterest
     * @param date $dob
     * @param date $subscriptiondate
     * @param int $postcode
     * @throws Exception
     * @return int
     */
    public function subscribeWithDetails($email, $firstname, $lastname, $mobile, $state, $periodicity, $jobinterest, $dob, $subscriptiondate, $postcode)
    {
        /*
        Mage::helper('campaignmonitor')->log(sprintf("%s->firstname=%s,lastname=%s,email=%s,mobile=%s,state=%s,periodicity=%s,jobinterest=%s,dob=%s",
            __METHOD__, $firstname, $lastname, $email, $mobile, $state, $periodicity, $jobinterest, $dob));
        */
        $helper = Mage::helper('campaignmonitor');
        
        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('customer/session');

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;
        $ownerId = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email)
            ->getId();
        $isSubscribeOwnEmail = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId();

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED || $this->getStatus() == self::STATUS_NOT_ACTIVE) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $isOwnSubscribes = $isSubscribeOwnEmail;
                if ($isOwnSubscribes == true){
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                }
                else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            }
            else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            // Set new values
            $this->setSubscriberFirstname($firstname);
            $this->setSubscriberLastname($lastname);
            $this->setSubscriberEmail($email);
            $this->setSubscriberMobile($mobile);
            $this->setSubscriberState($state);
            $this->setSubscriberPostcode($postcode);
            $this->setSubscriberPeriodicity($periodicity);
            $this->setSubscriberJobinterest($jobinterest);
            $this->setSubscriberDob($dob);
            $this->setSubscriberSubscriptiondate($subscriptiondate);
            
            if ($helper->isCouponEnabled()) {
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
        }

        if ($isSubscribeOwnEmail) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
        }
        else {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        try {
            $this->save();
            if ($isConfirmNeed === true && $isOwnSubscribes === false) {
                $this->sendConfirmationRequestEmail();
            }
            else {
                $this->sendConfirmationSuccessEmail();
            }

            return $this->getStatus();
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retrieve Subscribers Full Name if it was set
     *
     * @return string
     */
    public function getSubscriberFullName()
    {
        $name = "";
        $firstName = $this->getSubscriberFirstname();
        $lastName = $this->getSubscriberLastname();
        if (!empty($firstName) || !empty($lastName) ) {
            $name = sprintf("%s%s%s", $firstName, (!empty($lastName)?" ":""), $lastName);
        }
        else {
            $firstName = $this->getCustomerFirstname();
            $lastName = $this->getCustomerLastname();
            if (!empty($firstName) || !empty($lastName) ) {
                $name = sprintf("%s%s%s", $firstName, (!empty($lastName)?" ":""), $lastName);        
            }
        }
        //Mage::helper('campaignmonitor')->log(sprintf("%s->name='%s'[%d]", __METHOD__, $name, empty($name)) );
        return $name;
    }
    
    /**
     * Update already subscribed user with new details
     *
     * @return boolean
     */
    public function updateSubscription($firstname, $lastname, $email, $mobile, $state, $periodicity, $jobinterest, $dob, $postcode)
    {
        $session = Mage::getSingleton('core/session');
        // Test the email retrieved in the email and the email passed to the function
        if ($session->getEmail() == $email)
        {
            // Load subscriber
            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
            // Save for Magento backend
            if (isset($firstname)) $subscriber->setSubscriberFirstname($firstname);
            if (isset($lastname)) $subscriber->setSubscriberLastname($lastname);
            if (isset($mobile)) $subscriber->setSubscriberMobile($mobile);
            if (isset($state)) $subscriber->setSubscriberState($state);
            if (isset($postcode)) $subscriber->setSubscriberPostcode($postcode);
            if (isset($periodicity)) $subscriber->setSubscriberPeriodicity($periodicity);
            if (isset($jobinterest)) $subscriber->setSubscriberJobinterest($jobinterest);
            if (isset($dob)) $subscriber->setSubscriberDob($dob);
            // Save subscriber
            $subscriber->save();
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Retrieve Campaign Monitor Status
     *
     * @return array
     */
    public function getCampaignMonitorStatus()
    {
        $email = $this->getEmail();
        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
        if($apiKey && $listID && $email) 
        {
            try 
            {
                $client = new CS_REST_Subscribers($listID,$apiKey);
            } catch(Exception $e) {
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Error connecting to CampaignMonitor server: ".$e->getMessage());
                $session->addException($e, $this->__('There was a problem with the subscription'));
                $this->_redirectReferer();
            }
            try 
            {
                $result = $client->get($email);
                if ($result->was_successful() && $result->response->State) return $result->response->State;
                else return "Not Subscribed";
            } catch(Exception $e) {
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in CampaignMonitor SOAP call: ".$e->getMessage());
                $session->addException($e, $this->__('There was a problem with the subscription'));
                $this->_redirectReferer();
            }
        }
    }
    
    /**
     * Retrieve Campaign Monitor Status
     *
     * @return array
     */
    public function getCampaignMonitorHash()
    {
        $email = $this->getEmail();
        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
        if($apiKey && $listID && $email) 
        {
            try 
            {
                $client = new CS_REST_Subscribers($listID,$apiKey);
            } catch(Exception $e) {
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Error connecting to CampaignMonitor server: ".$e->getMessage());
                $session->addException($e, $this->__('There was a problem with the subscription'));
                $this->_redirectReferer();
            }
            try 
            {
                $result = $client->get($email);
                if ($result->was_successful() && $result->response->CustomFields) 
                {
                    foreach ($result->response->CustomFields as $customField)
                    {
                        if ($customField->Key == "securehash") return $customField->Value;
                    }
                }
                else return false;
            } catch(Exception $e) {
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in CampaignMonitor SOAP call: ".$e->getMessage());
                $session->addException($e, $this->__('There was a problem with the subscription'));
                $this->_redirectReferer();
            }
        }
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
        $from_date = date("Y-m-d");
        
        // Add two weeks
        $to_date = date('Y-m-d', strtotime(date("Y-m-d", strtotime($from_date)) . " +".$validdays." days"));
        
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
            ->setFromDate($from_date)
            ->setToDate($to_date)
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
}
