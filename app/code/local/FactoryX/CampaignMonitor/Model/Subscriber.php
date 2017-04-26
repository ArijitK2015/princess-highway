<?php
/**
 * Class FactoryX_CampaignMonitor_Model_Subscriber
 */
class FactoryX_CampaignMonitor_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    /**
     * Get the helper
     * @return FactoryX_CampaignMonitor_Helper_Data
     */
    protected function _hlp()
    {
        return Mage::helper('campaignmonitor');
    }

    /**
     * Get the CM helper
     * @return FactoryX_CampaignMonitor_Helper_Cm
     */
    protected function _hlpCM()
    {
        return Mage::helper('campaignmonitor/cm');
    }

    /**
     * Subscribe the user to Magento
     * $params : all the content from the form, add element into the mapping to map them with database
     * empty means just ignore
     * $first - using for creating the coupon code for first time subscriber
     * $confirmEmail : are we sending email to the subscriber for successful subscription?
     * @param $params
     * @param bool $confirmEmail
     * @param bool $sync
     * @param bool $update
     * @param bool $first
     * @return bool|int
     * @throws Exception
     */
    public function subscribeWithDetails($params,$confirmEmail = true, $sync = false, $update = false, $first = false){
        try {
            // No email => stop here
            if (!$params['email']) return false;
            else $email = $params['email'];

            // Check if loaded
            if (!$this->getSubscriberId())
            {
                // Load if not
                $this->loadByEmail($email);
            }

            // Is this an unsubscription
            if (isset($params['unsubscribe']) && $params['unsubscribe'] == 1)
            {
                $this->_unsubscribe();
                return true;
            }

            // Is this an update
            if ($update)
            {
                $this->_update($params);
                return true;
            }

            // Is this a sync
            if ($sync)
            {
                // Mapping between CM and MAGENTO subscriber model
                $mapping = $this->_hlp()->generateMapping('campaignmonitor','subscriber');
                $params = $this->_hlpCM()->getCMData($email);
            }
            else
            {
                // Mapping between FORM and MAGENTO subscriber model
                $mapping = $this->_hlp()->generateMapping('formfields','subscriber');
                // For some reason the DOB is not playing nice from the form, so we clean them
                if (array_key_exists('dob',$params) && $params['dob'])
                {
                    $date = new Zend_Date($params['dob'], 'd/M/Y');
                    $params['dob'] = $date->toString('M/d/Y');
                }
            }

            // Map mobile to yes if value = 1
            if ((isset($params['is_subscribed_sms'])
                    && $params['is_subscribed_sms'] == 1)
                || !empty($params['mobile'])
                || !empty($params['Mobile']))
            {
                $params['is_subscribed_sms'] = 'YES';
            }

            // Set the data from form to model
            foreach ($params as $key => $param)
            {
                if (!isset($mapping[$key]))
                {
                    // this is some information that we won't know how to store in magento, so we log
                    $this->_hlp()->log("FactoryX_CampaignMonitor_Model_Subscriber: ".$key." is not defined in magento subscriber mapping.");
                }
                elseif (!empty($param) && !empty($mapping[$key]))
                {
                    $this->setData($mapping[$key], $param);
                }
            }

            // Set securehash
            $this->setData('subscriber_securehash',md5($email.$this->_hlp()->getApiKey()));
            // Set subscription date
            $this->setData('subscriber_subscriptiondate',date("Y-m-d H:i:s"));

            // Preferred store
            if (Mage::helper('core')->isModuleEnabled('FactoryX_StoreLocator')) {
                $ip = $this->_hlp()->getRemoteAddr();
                if ($store = Mage::helper('ustorelocator')->getLocationByIpAddress($ip)) {
                    $storeCode = $store->getData('store_code');
                    $this->setData('subscriber_preferredstore', $storeCode);
                }
            }

            // Coupon stuff
            if ((!$sync || $first) && $confirmEmail)
            {
                Mage::helper('campaignmonitor/coupon')->handleCoupon($this);
            }

            // Set subscriber status to subscribed
            $this->setStatus(self::STATUS_SUBSCRIBED);
            $this->setIsStatusChanged(true);

            // Assign customer id
            $this->_assignCustomer($email);

            // Set store
            $this->setStoreId(Mage::app()->getStore()->getId());

            // Save
            $this->save();

            // Send confirmation email
            if ($confirmEmail || $first)
            {
                $lists = $this->_hlp()->getLists($params);
                $this->sendBrandConfirmationSuccessEmail($lists);
            }
            return $this->getStatus();
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Subscriber subscriber to CM
     *
     * note. there is a 5 minute lag between submit and CM subscription
     *
     * $params: array of data to pass into CM
     * $sync: sync from Magento, hence need to change the mapping
     * @param $params
     * @param bool $sync
     * @param bool $update
     * @return bool
     * @throws Exception
     */
    public function subscribeWithDetailsCM($params, $sync = false, $update = false)
    {
        $lists = $this->_hlp()->getLists($params);

        // Is this an unsubscription
        if (isset($params['unsubscribe']) && $params['unsubscribe'] == 1)
        {
            foreach ($lists as $listId)
            {
                $this->_hlpCM()->unsubscribe($params['email'],$listId);
            }
            return true;
        }

        // Is this a sync
        $prefix = '';
        if ($sync)
        {
            $email = $params['email'];
            // Mapping between MAGENTO and CM
            $mapping = $this->_hlp()->generateMapping('subscriber', 'campaignmonitor');

            // Check if loaded
            if (!$this->getSubscriberId())
            {
                // Load if not
                $this->loadByEmail($email);
            }

            if (!$this->getSubscriberId()) return false;

            // Setting secure hash just to be safe
            $securehash = $this->getData('subscriber_securehash');
            if (empty($securehash))
            {
                $this->setData('subscriber_securehash',md5($email.$this->_hlp()->getApiKey()));
                $this->save();
            }
            $params = $this->getData();
            $prefix = 'subscriber_';
        }
        else {
            // mapping between FORM and CM
            $mapping = $this->_hlp()->generateMapping('formfields', 'campaignmonitor');
        }

        $this->_hlp()->log(sprintf("%s->form: %s", __METHOD__, print_r($params, true) ));
        $this->_hlp()->log(sprintf("%s->mapping: %s", __METHOD__, print_r($mapping, true) ));

        // Generate the custom fields from params and mapping
        $customFields = $this->_hlp()->generateCustomFields($params,$mapping,$update);

        // Is this an update
        if ($update)
        {
            try
            {
                $this->_updateCm($params,$lists,$customFields);
            }
            catch (Exception $e)
            {
                $this->_hlp()->log("Error in CampaignMonitor REST call: ".$e->getMessage());
                return false;
            }
            return true;
        }

        // Name handling
        $name = "(Guest)";
        if (array_key_exists('firstname', $params) && array_key_exists('lastname', $params) && $params['firstname'] && $params['lastname']) {
            $name = $params['firstname'] . ' ' . $params['lastname'];
        } elseif (array_key_exists('name', $params) && $params['name']) {
            $name = $params['name'];
        }

        // If not a sync
        if (!$sync)
        {
            // add secure hash
            $customFields[] = array(
                "Key"   => "securehash",
                "Value" => md5($params['email'].$this->_hlp()->getApiKey())
            );
        }

        // Preferred store
        if (Mage::helper('core')->isModuleEnabled('FactoryX_StoreLocator')) {
            $ip = $this->_hlp()->getRemoteAddr();
            if ($store = Mage::helper('ustorelocator')->getLocationByIpAddress($ip)) {
                $customFields[] = array(
                    'Key' => 'From Store',
                    'Value' => $store->getData('store_code')
                );
            }
        }

        // Add new
        try
        {
            $this->_hlp()->log(sprintf("%s->customFields: %s", __METHOD__, print_r($customFields, true) ));
            foreach ($lists as $listId)
            {
                $result = $this->_hlpCM()->add($params[$prefix.'email'],$name,$customFields,$listId);
                $this->_hlp()->log(sprintf("%s->result: %s", __METHOD__, print_r($result, true) ));
            }
        }
        catch (Exception $e) {
            $session = Mage::getSingleton('core/session');
            $this->_hlp()->log("Error in CampaignMonitor REST call: ".$e->getMessage());
            $session->addException($e, $this->_hlp()->__('There was a problem with the subscription'));
            return false;
        }
        return true;
    }

    /**
     * @param $email
     * @param bool $first
     * @throws Exception
     */
    public function syncSubscriber($email,$first = false)
    {
        // Avalaible?
        $magentoAvailable = false;
        $cmAvailable = false;

        // Get subscriber from Magento
        $subscriberModel = $this->loadByEmail($email);
        if ($subscriberModel && $subscriberModel->getStatus() == self::STATUS_SUBSCRIBED) $magentoAvailable = true;

        // Get subscribder from CM
        $cmModel = $this->_hlpCM()->getCMData($email);
        if (!is_null($cmModel))
        {
            $cmAvailable = true;
        }

        // If missing from CM, we sync from Magento
        if (!$cmAvailable)
        {
            $this->subscribeWithDetailsCM(array('email'=>$email),true);
            return;
        }

        // If missing from Magento, we sync from CM
        if (!$magentoAvailable)
        {
            $this->subscribeWithDetails(array('email'=>$email),false,true,false,$first);
            return;
        }

        // mapping between CM and MAGENTO
        $mapping = $this->_hlp()->generateMapping('campaignmonitor','subscriber');
        $mage_array = array();
        $cm_array = array();

        foreach($mapping as $cm => $mage)
        {
            if (array_key_exists($cm,$cmModel))
            {
                $this->syncValue($this->getData($mage),$cmModel[$cm],$mage,$cm,$mage_array,$cm_array);
            }
        }

        // Do we need to update CM?
        if (!empty($cm_array))
        {
            $cm_array['email'] = $email;
            $this->subscribeWithDetailsCM($cm_array, false, true);
        }

        // Do we need to update Mage?
        if (!empty($mage_array))
        {
            $mage_array['email'] = $email;
            $this->subscribeWithDetails($mage_array,false,false,true);
        }

        return;
    }


    /**
     * Select which value to sync
     * Always treat the CM as master
     * @param $mage_val string Magento value
     * @param $cm_val   string CM value
     * @param $mage_key string Magento data name
     * @param $cm_key   string CM fields name
     * @param $mage_array   array to add in if there is new information for mage
     * @param $cm_array array to add in if there is new information for CM
     */
    private function syncValue($mage_val, $cm_val, $mage_key, $cm_key, &$mage_array, &$cm_array)
    {
        // Cases that we should not need to sync value
        if ((empty($mage_val) && empty($cm_val)) || ($mage_val == $cm_val)) return;
        $this->_hlp()->log('update happens: '.$mage_val.' '.$cm_val);
        if (empty($cm_val))
        {
            $cm_array[] = array("Key" => $cm_key, "Value" => $mage_val);
        }else
        {
            $mage_array[$mage_key] = $cm_val;
        }
        return;
    }

    /**
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer)
    {
        if ((bool) Mage::getSingleton('checkout/session')->getCustomerIsSubscribed())
        {
            $customer->setIsSubscribed(1);
        }

        // Original Customer subscription
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

        // Set securehash
        $this->setData('subscriber_securehash',md5($customer->getEmail().$this->_hlp()->getApiKey()));
        // Set subscription date
        $this->setData('subscriber_subscriptiondate',date("Y-m-d H:i:s"));

        // Coupon generation
        Mage::helper('campaignmonitor/coupon')->handleCoupon($this);

        $this->save();
        $sendSubscription = $customer->getData('sendSubscription') || $sendInformationEmail;
        if (is_null($sendSubscription) xor $sendSubscription) {
            if ($this->getIsStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                $this->sendUnsubscriptionEmail();
            } elseif ($this->getIsStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
                // Send the email
                $lists = $this->_hlp()->getLists(['brands' => 'all']);
                $this->sendBrandConfirmationSuccessEmail($lists);
            }
        }

        return $this;
    }

    /**
     * Retrieve all customer attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        // Fetch write database connection that is used in Mage_Core module
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        // Now $write is an instance of Zend_Db_Adapter_Abstract
        $result = $write->query("select distinct column_name from information_schema.columns where table_name = 'newsletter_subscriber' order by ordinal_position");
        $array = array();
        while ($row = $result->fetch())
        {
            $array[] = $row['column_name'];
        }
        return $array;
    }

    /**
     * Mage unsubscription
     * @return $this
     */
    protected function _unsubscribe()
    {
        $this->setStatus(self::STATUS_UNSUBSCRIBED);
        $this->save();
        return $this;
    }

    /**
     * Mage update
     * @param $params
     * @return $this
     */
    protected function _update($params)
    {
        foreach ($params as $key => $value)
        {
            $this->setData($key,$value);
        }
        $this->save();
        return $this;
    }

    /**
     * Asssign customer to subscriber
     * @param $email
     */
    protected function _assignCustomer($email)
    {
        // Check if the customer is logged in and the email is matched
        if (Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $customer = Mage::getSingleton('customer/session');
            $collection = Mage::getResourceModel('customer/customer_collection')
                ->addFieldToFilter('entity_id', array($customer->getId()))
                ->addAttributeToSelect(array('email'))
                ->setPageSize(1);

            $customerEmail = $collection->getFirstItem()->getEmail();
            if ($email == $customerEmail){
                $this->setCustomerId($customer->getId());
            }
        }
    }

    /**
     * CM Update
     * @param $params
     * @param $lists
     * @param $customFields
     */
    protected function _updateCm($params,$lists,$customFields)
    {
        // is there a new email ?
        $email = $params['email'];
        $new_email = null;
        if (isset($params['new_email']))
        {
            $new_email = $params['new_email'];
        }
        else
        {
            $new_email = $email;
        }
        foreach ($lists as $listId)
        {
            $params = $this->_hlp()->handleFullName($params);
            $this->_hlpCM()->update($email,$customFields,$new_email,$params['firstname']." ".$params['lastname'],$listId);
        }
    }

    /**
     * Sends out confirmation success email
     *
     * @param $lists
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function sendBrandConfirmationSuccessEmail($lists)
    {
        $email_templates = $this->_hlp()->getTemplates($lists);
        if(empty($email_templates)) {
            return parent::sendConfirmationSuccessEmail();
        }

        if ($this->getImportMode()) {
            return $this;
        }

        foreach ($email_templates as $email_template){
            if(!Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY)) continue;

            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $email = Mage::getModel('core/email_template');

            $email->sendTransactional(
                $email_template,
                Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY),
                $this->getEmail(),
                $this->getName(),
                array('subscriber'=>$this)
            );

            $translate->setTranslateInline(true);
        }

        return $this;
    }

}