<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_subscribers.php";
class SubscriberCustomField
{
    function SubscriberCustomField($k, $v)
    {
        $this->Key = $k;
        $this->Value = $v;
    }
}

class FactoryX_CampaignMonitor_Model_Customer_Observer
{
    public function check_subscription_status($observer)
    {   
        $event = $observer->getEvent();
        $customer = $event->getCustomer();

        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
        
        $name = $customer->getFirstname() . " " . $customer->getLastname();
        $newEmail = $customer->getEmail();
        $subscribed = $customer->getIsSubscribed();
       

        $oldEmail = Mage::getModel('customer/customer')->load($customer->getId())->getEmail();
        // if subscribed is NULL (i.e. because the form didn't set it one way
        // or the other), get the existing value from the database
        if($subscribed === NULL)
        {
            $subscribed = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer)->isSubscribed();
            //print "\$subscribed is NULL, using old value: $subscribed\n<br />";
        }

        //print "Name: $name, New email: $newEmail, Subscribed: $subscribed, Old email: $oldEmail<br />\n";

        if($apiKey and $listID)
        {
            $customFields = FactoryX_CampaignMonitor_Model_Customer_Observer::generateCustomFields($customer);
            
            try {
                $client = new CS_REST_Subscribers($listID,$apiKey);
            } catch(Exception $e) {
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Error connecting to CampaignMonitor server: ".$e->getMessage());
                return;
            }

            if($subscribed)
            {
                /* If the customer:
                   
                   1) Already exists (i.e. has an old email address)
                   2) Has changed their email address
                    
                   unsubscribe their old address. */
                if ($oldEmail and $newEmail != $oldEmail)
                {
                    Mage::helper('campaignmonitor')->log(__METHOD__ . "Unsubscribing old email address: $oldEmail");
                    try {
                        $result = $client->unsubscribe($oldEmail);
                    } catch(Exception $e) {
                        Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in SOAP call: ".$e->getMessage());
                        return;
                    }
                }
            
                // Using 'add and resubscribe' rather than just 'add', otherwise
                // somebody who unsubscribes and resubscribes won't be put back
                // on the active list
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Subscribing new email address: $newEmail");
                try {
                    $result = $client->add(array(
                            "EmailAddress" => $newEmail,
                            "Name" => $name,
                            "CustomFields" => $customFields,
							"Resubscribe" => true));
                } catch(Exception $e) {
                    Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in SOAP call: ".$e->getMessage());
                    return;
                }
            }
            else
            {
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Unsubscribing: $oldEmail");
                
                try {
                    $result = $client->unsubscribe($oldEmail);
                } catch(Exception $e) {
                    Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in SOAP call: ".$e->getMessage());
                    return;
                }
            }
        }
    }

    public function customer_deleted($observer)
    {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();

        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
       
        $email = $customer->getEmail();

        if($apiKey and $listID)
        {
            Mage::helper('campaignmonitor')->log(__METHOD__ . "Customer deleted, unsubscribing: $email");
            try {
                $client = new CS_REST_Subscribers($listID,$apiKey);
                $result = $client->unsubscribe($email);
            } catch(Exception $e) {
                Mage::helper('campaignmonitor')->log(__METHOD__ . "Error in SOAP call: ".$e->getMessage());
                return;
            }
        }
    }
    
    // get array of linked attributes from the config settings and
    // populate it
    public static function generateCustomFields($customer)
    {
        $linkedAttributes = @unserialize(Mage::getStoreConfig('newsletter/campaignmonitor/m_to_cm_attributes',
                Mage::app()->getStore()->getStoreId()));
        $customFields = array();
        if(!empty($linkedAttributes))
        {
            $customerData = $customer->getData();
            foreach($linkedAttributes as $la)
            {
                $magentoAtt = $la['magento'];
                $cmAtt = $la['campaignmonitor'];
               
                // try and translate IDs to names where possible
                if($magentoAtt == 'group_id')
                {
                    $d = Mage::getModel('customer/group')->load($customer->getGroupId())->getData();
                    if(array_key_exists('customer_group_code', $d))
                    {
                        $customFields[] = array("Key" => $cmAtt, "Value" => $d['customer_group_code']);
                    }
                }
                else if($magentoAtt == 'website_id')
                {
                    $d = Mage::getModel('core/website')->load($customer->getWebsiteId())->getData();
                    if(array_key_exists('name', $d))
                    {
                        $customFields[] = array("Key" => $cmAtt, "Value" => $d['name']);
                    }
                }
                else if($magentoAtt == 'store_id')
                {
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

        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $customFields[] = array("Key" => 'securehash', "Value" => md5($customer->getEmail().$apiKey));
        $customFields[] = array("Key" => "DateOfBirth", "Value" => $customer->getDob());
        return $customFields;
    }
}
