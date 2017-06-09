<?php
/**
 * Class FactoryX_CampaignMonitor_Model_Checkout_Observer
 */
class FactoryX_CampaignMonitor_Model_Checkout_Observer
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
     * Subscribe a customer after placing an order
     * @param $observer
     */
    public function subscribeCustomer(Varien_Event_Observer $observer)
    {
        try {
            // Only if the checkbox has been ticked using the sessions
            if ((bool) Mage::getSingleton('checkout/session')->getCustomerIsSubscribed())
            {
                // Get the quote & customer
                $quote = $observer->getEvent()->getQuote();
                $order = $observer->getEvent()->getOrder();

                switch ($quote->getCheckoutMethod())
                {
                    case "guest":
                        $customer = false;
                        $email = $order->getCustomerEmail();
                        break;
                    case "register":
                    default:
                        $customer = $order->getCustomer();
                        // Get its email
                        $email = $customer->getEmail();
                        break;
                }

                // Check if already susbcribed
                $cmStatus = $this->_hlpCM()->getCMStatus($email);

                // If we are not already subscribed in Campaign Monitor
                if ($cmStatus != 2)
                {
                    // Generate the custom fields from the quote
                    $customFields = $this->_generateCustomFields($quote);
                    // Generate the hash
                    $customFields[] = array(
                        "Key"   => "securehash",
                        "Value" => md5($email . $this->_hlp()->getApiKey())
                    );
                    // Generate the name
                    $name = ($customer ? $customer->getName() : $quote->getBillingAddress()->getName());
                    $customFields[] = array(
                        "Key"   => "fullname",
                        "Value" => $name
                    );

                    // Subscribe to Campaign Monitor
                    $this->_hlpCM()->add($email,$name,$customFields);
                    // Get the corresponding subscriber
                    if ($customer) {
                        // Sync the subscriber
                        $subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
                        $subscriber->syncSubscriber($email,false);
                    }
                    else {
                        // Sync the subscriber
                        Mage::getModel('newsletter/subscriber')->syncSubscriber($email,false);
                        // Load the subscriber
                        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                        // Sometimes the CM server does not handle the subscription straight away
                        // Thus the sync does not work which can creates empty email subscriber
                        // As the load above is assuming the Magento subscriber has been created during the sync
                        // To avoid that we still create a new subscriber only based on the email
                        /** @TODO create a way to sync from $customFields instead of syncing from the CM server **/
                        if (!$subscriber->getSubscriberId()) {
                            Mage::getModel('newsletter/subscriber')->subscribeWithDetails(array('email' => $email), false, false, false, false);
                            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                        }
                        // Coupon generation
                        Mage::helper('campaignmonitor/coupon')->handleCoupon($subscriber);
                        $subscriber->save();
                        // Resync the subscriber to add the coupon
                        Mage::getModel('newsletter/subscriber')->syncSubscriber($email,false);
                        // Send the email
                        $lists = $this->_hlp()->getLists(['brands' => 'all']);
                        $subscriber->sendBrandConfirmationSuccessEmail($lists);
                    }
                }

                // Remove the session variable
                Mage::getSingleton('checkout/session')->setCustomerIsSubscribed(0);
            }
        }
        catch(Exception $ex) {
            // do nothing
            Mage::helper('campaignmonitor')->log(sprintf("Error: %s", $ex->getMessage()) );
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function checkIfSubscribed(Varien_Event_Observer $observer)
    {
        // Get the post data
        $post = $observer->getControllerAction()->getRequest()->getPost();
        // IWD Case
        if (array_key_exists('is_subscribed',$post) && isset($post['is_subscribed']) && !empty($post['is_subscribed']))
        {
            Mage::getSingleton('checkout/session')->setCustomerIsSubscribed(1);
        }
        // Flag the customer has subscribed if he ticked the box
        elseif (array_key_exists('billing',$post) && array_key_exists('is_subscribed',$post['billing']) && isset($post['billing']['is_subscribed']) && !empty($post['billing']['is_subscribed']))
        {
            Mage::getSingleton('checkout/session')->setCustomerIsSubscribed(1);
        }
        else
        {
            Mage::getSingleton('checkout/session')->setCustomerIsSubscribed(0);
        }
    }

    /**
     * Generate custom fields based on a quote
     * @param $quote
     * @return array
     */
    protected function _generateCustomFields($quote)
    {
        $customFields = array();
        // Mobile
        if ($mobile = $quote->getBillingAddress()->getTelephone())
        {
            $customFields[] = array("Key" => "Mobile", "Value" => $mobile);
        }
        // State and country
        $state = $quote->getBillingAddress()->getRegion();
        $country = $quote->getBillingAddress()->getCountryId();
        if ($state || $country)
        {
            $campaignMonitorStates = $this->_hlp()->getCampaignMonitorStates();
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
        // Postcode
        if ($postcode = $quote->getBillingAddress()->getPostcode())
        {
            $customFields[] = array("Key" => "Postcode", "Value" => $postcode);
        }
        // Date of birth
        if ($dob = $quote->getCustomerDob())
        {
            $customFields[] = array("Key" => "DateOfBirth", "Value" => $dob);
        }
        // Name
        $customFields[] = array("Key" => "fullname", "Value" => $quote->getBillingAddress()->getName());

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