<?php
/**
 * Class FactoryX_CampaignMonitor_Model_Customer_Observer
 */
class FactoryX_CampaignMonitor_Model_Customer_Observer
{
    /**
     * Get the CM helper
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
     * Check the subscription status of a customer before save
     * @param $observer
     */
    public function checkSubscriptionStatus(Varien_Event_Observer $observer)
    {
        // Get the event
        $event = $observer->getEvent();
        // Get the customer
        $customer = $event->getCustomer();

        // Customer info
        $name = $customer->getFirstname() . " " . $customer->getLastname();
        $newEmail = $customer->getEmail();
        $subscribed = $customer->getIsSubscribed();

        // Get the info from the database to compare if email has changed
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addFieldToFilter('entity_id', array($customer->getId()))
            ->addAttributeToSelect(array('email'))
            ->setPageSize(1);
        $oldEmail = $collection->getFirstItem()->getEmail();

        // That means it is a new customer so we stop here
        if (empty($oldEmail)) return;

        // Generate the custom fields
        $customFields = $this->_generateCustomFields($customer);

        if($subscribed)
        {
            /*
             * If the customer:
             * 1) Already exists (i.e. has an old email address)
             * 2) Has changed their email address
             * 3) Unsubscribe their old address.
             */
            if ($oldEmail and $newEmail != $oldEmail)
            {
                $this->_hlp()->log("Unsubscribing old email address: $oldEmail");
                $this->_hlpCM()->unsubscribe($oldEmail);
            }

            /*
             * Using 'add and resubscribe' rather than just 'add', otherwise
             * somebody who unsubscribes and resubscribes won't be put back
             * on the active list
             */
            $this->_hlp()->log("Subscribing new email address: $newEmail");
            $this->_hlpCM()->add($newEmail,$name,$customFields);
        }
        else
        {
            // Simply unsubscribe
            $this->_hlp()->log("Unsubscribing: $oldEmail");
            $this->_hlpCM()->unsubscribe($oldEmail);
        }
        // Synchronise the subsriber
        Mage::getModel('newsletter/subscriber')->syncSubscriber($newEmail);
    }

    /**
     * Unsubscribe a customer before it gets deleted
     * @param $observer
     */
    public function customerDeleted(Varien_Event_Observer $observer)
    {
        // Get the event
        $event = $observer->getEvent();
        // Get the customer
        $customer = $event->getCustomer();

        // Get the customer email
        $email = $customer->getEmail();

        // Unsubscribe
        $this->_hlp()->log("Customer deleted, unsubscribing: $email");
        $this->_hlpCM()->unsubscribe($email);
    }

    /**
     * Subscribe a customer who subscribed to Mage after a successfull registration
     * @param $observer
     */
    public function subscribeCustomer(Varien_Event_Observer $observer)
    {
        // Get the customer
        $customer = $observer->getEvent()->getCustomer();
        // Get its email
        $email = $customer->getEmail();
        // Check if subscribed
        $hasSubscribed = $customer->getIsSubscribed();

        // Check if the customer has subscribed via OG magento checkbox
        if ($hasSubscribed)
        {
            // Check if already susbcribed
            $cmStatus = $this->_hlpCM()->getCMStatus($email);

            // If we are not already subscribed in Campaign Monitor
            if ($cmStatus != 2)
            {
                // Generate mapping
                $mapping = $this->_hlp()->generateMapping('magento','campaignmonitor');
                // Generate the custom fields from params and mapping
                $customFields = $this->_hlp()->generateCustomFields($customer->getData(),$mapping);
                // Generate the hash
                $customFields[] = array("Key" => "securehash", "Value" => md5($email.$this->_hlp()->getApiKey()));
                // Generate the name
                $customFields[] = array("Key" => "fullname", "Value" => $customer->getName());
                // Subscribe to Campaign Monitor
                $this->_hlpCM()->add($email,$customer->getName(),$customFields);
            }
        }
    }

    /**
     * Get array of linked attributes from the config settings and populate it
     * @param $customer
     * @return array
     * @TODO avoid loading models
     */
    protected function _generateCustomFields($customer)
    {
        $linkedAttributes = unserialize(Mage::getStoreConfig('newsletter/campaignmonitor/m_to_cm_attributes',
            Mage::app()->getStore()->getStoreId()));
        $customFields = array();
        if(!empty($linkedAttributes))
        {
            // Get customer data
            $customerData = $customer->getData();
            // Loop through the linked attributes
            foreach($linkedAttributes as $la)
            {
                // Mage attr
                $magentoAtt = $la['magento'];
                // CM attr
                $cmAtt = $la['campaignmonitor'];

                // Try and translate IDs to names where possible
                if($magentoAtt == 'group_id')
                {
                    // Load customer group to get its code
                    $d = Mage::getModel('customer/group')->load($customer->getGroupId())->getData();
                    if(array_key_exists('customer_group_code', $d))
                    {
                        $customFields[] = array("Key" => $cmAtt, "Value" => $d['customer_group_code']);
                    }
                }
                else if($magentoAtt == 'website_id')
                {
                    // Load website to get its name
                    $d = Mage::getModel('core/website')->load($customer->getWebsiteId())->getData();
                    if(array_key_exists('name', $d))
                    {
                        $customFields[] = array("Key" => $cmAtt, "Value" => $d['name']);
                    }
                }
                else if($magentoAtt == 'store_id')
                {
                    // Load store to get its name
                    $d = Mage::getModel('core/store')->load($customer->getStoreId())->getData();
                    if(array_key_exists('name', $d))
                    {
                        $customFields[] = array("Key" => $cmAtt, "Value" => $d['name']);
                    }
                }
                else if(strncmp('FACTORYX', $magentoAtt, 6) == 0)
                {
                    $d = false;
                    // 15 == strlen('FACTORYX-billing-')
                    if(strncmp('FACTORYX-billing', $magentoAtt, 14) == 0)
                    {
                        $d = $customer->getDefaultBillingAddress();
                        if($d)
                        {
                            $d = $d->getData();
                            $addressAtt = substr($magentoAtt, 15, strlen($magentoAtt));
                        }
                    }
                    // 16 == strlen('FACTORYX-shipping-')
                    else
                    {
                        $d = $customer->getDefaultShippingAddress();
                        if($d)
                        {
                            $d = $d->getData();
                            $addressAtt = substr($magentoAtt, 16, strlen($magentoAtt));
                        }
                    }

                    if($d and $addressAtt == 'country_id')
                    {
                        if(array_key_exists('country_id', $d))
                        {
                            $country = Mage::getModel('directory/country')->load($d['country_id']);
                            $customFields[] = array("Key" , $d=> $cmAtt, "Value" => $country->getName());
                        }
                    }
                    else if($d)
                    {
                        if(array_key_exists($addressAtt, $d))
                        {
                            $customFields[] = array("Key" => $cmAtt, "Value" => $d[$addressAtt]);
                        }
                    }
                }
                else
                {
                    if(array_key_exists($magentoAtt, $customerData))
                    {
                        $customFields[] = array("Key" => $cmAtt, "Value" => $customerData[$magentoAtt]);
                    }
                }
            }
        }

        $customFields[] = array("Key" => 'securehash', "Value" => md5($customer->getEmail().$this->_hlp()->getApiKey()));
        $customFields[] = array("Key" => "DOB", "Value" => $customer->getDob());

        // Add the default interest
        if ($defaultInterests = $this->_hlp()->getDefaultInterests()) {
            foreach($defaultInterests as $interest) {
                $customFields[] = array(
                    "Key"   => "interests",
                    "Value" => $interest
                );
            }
        }

        return $customFields;
    }
}
