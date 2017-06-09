<?php
require_once 'Mage/Newsletter/controllers/SubscriberController.php';

/**
 * Class FactoryX_CampaignMonitor_SubscriberController
 * Overidde the original subscription controller
 */
class FactoryX_CampaignMonitor_SubscriberController extends Mage_Newsletter_SubscriberController
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
     * This is the subscribe page
     * A redirect to /subscribe is automatically generated via SQL script on 1.0.0 install
     */
    public function indexAction()
    {
        $this->loadLayout();
        // If the hash is provided and customer is logged
        if ((array_key_exists("HTTP_REFERER", $_SERVER))
            && (strpos($_SERVER["HTTP_REFERER"],"key=") === false)
            && Mage::getSingleton('customer/session')->isLoggedIn()
            && !(isset($_GET['key'])))
        {
            // We need to redirect with the email added to the URL
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $email = $customer->getEmail();
            $apiKey = Mage::helper('campaignmonitor')->getApiKey();
            $url = sprintf("%ssubscribe?email=%s&key=%s", Mage::getBaseUrl(), $email, md5($email.$apiKey));
            $this->_redirectUrl($url);
        }
        $this->getLayout()->getBlock('head')->setTitle('Subscribe');
        $this->renderLayout();
    }


    /**
     * To be used in the front end via AJAX call
     * or be used within this class with parameter $email
     *
     * Return a response array with status and message
     * @param null $email
     * @param bool $response
     * @return string
     */
    public function retrieveAction($email = null, $response = true)
    {

        // Parameters given in AJAX via GET
        $params = $this->getRequest()->getParams();

        // If the email is provided, we get it
        if (array_key_exists('email',$params)){
            $email = $params['email'];
        }

        // If the securehash is provided, we get it
        if (array_key_exists('securehash',$params)){
            $securehash = $params['securehash'];
        }
        elseif (array_key_exists('hidden_hash',$params)){
            $securehash = $params['hidden_hash'];
        }

        // If the email is not valid, return the response and exit now
        if (!Zend_Validate::is($email, 'EmailAddress')) {
            if ($response)
            {
                $this->_putResponse("ERROR","Please enter a valid email address.");
                return false;
            }
            else return "ERROR";
        }

        // Magento status
        $magentoStatus = $this->_hlp()->getMagentoStatus($email);

        // Campaign Monitor status
        $cmStatus = $this->_hlpCM()->getCMStatus($email);

        // Something went terribly wrong
        if (is_null($magentoStatus) || is_null($cmStatus))
        {
            if ($response) {
                $this->_putResponse("ERROR", "We cannot process your subscription at the moment, please try again later.");
                return false;
            }
            else return "ERROR";
        }
        // One of them is definitely subscribed
        elseif ($magentoStatus == 2 || $cmStatus == 2)
        {
            // Load the subscriber
            $subscriberModel = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
            // We need to synchronise the info
            $subscriberModel->syncSubscriber($email);

            // Check the securehash
            if (isset($securehash) && $securehash == md5($email.$this->_hlp()->getApiKey()))
            {
                if ($response)
                {
                    // Get the data from CM
                    $response = $this->_hlpCM()->getCMData($email);
                    $response['status'] = "EXISTING";
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                    return true;
                }
                else return "EXISTING";
            }
            else
            {
                if ($response)
                {
                    // If the subscriber appears in any of those source, we shouldn't appear to re-subscribe
                    $this->_putResponse("ERROR", "You have already subscribed. Please visit the link in our newsletter to update your details.");
                    return false;
                }
                else return "ERROR";
            }
        }
        // New subscription
        else
        {
            if ($response)
            {
                $this->_putResponse("NEW", "You have not subscribed to our newsletter yet, you can fill the form below to do so.");
                return true;
            }
            else return "NEW";
        }
        return false;
    }

    /**
     * New subscription action called when subscribing
     * Used for both page and mini subscribtion
     */
    public function newAction()
    {
        // Get all the parameters
        $params = $this->getRequest()->getParams();

        // Get session
        $session = Mage::getSingleton('core/session');

        // Check if it's a new subscription
        $status = $this->retrieveAction($params['email'], false);
        $isNew = ($status == "NEW");
        $isExisting = ($status == "EXISTING");

        // No welcome email
        $welcome = (array_key_exists('welcome',$params) ? false : true);
        $welcome = ($isNew ? $welcome : false);

        // Subscriber model
        $subscriberModel = Mage::getModel('newsletter/subscriber');

        // Popup subscription
        $popup = ($this->getRequest()->getPost('popup') ? true : false);
        $params['popup'] = $popup;

        // Fullname handler
        $params = $this->_hlp()->handleFullName($params);

        // Subscribe to Magento
        $mageSub = $subscriberModel->subscribeWithDetails($params,$welcome,false,$isExisting);
        // Subscribe to CM
        $cmSub = $subscriberModel->subscribeWithDetailsCM($params,false,$isExisting);

        // New subscription
        if ($isNew)
        {
            // Check if it worked and if it is not a popup
            if ($mageSub && $cmSub && !$popup)
            {
                // Success message
                $session->addSuccess($this->__('Thank you for your subscription.'));
            }
            elseif (!$popup)
            {
                // If it doesn't work but still not a popup, display error
                $session->addException(new Exception("There was a problem with the subscription."), $this->__('There was a problem with the subscription.'));
            }
        }
        // Subscription update / Unsubscription
        else
        {
            // Check if it worked
            if ($mageSub && $cmSub)
            {
                // Unsubscription
                if (array_key_exists('unsubscribe',$params) && $params['unsubscribe'] == 1)
                {
                    $session->addSuccess($this->__('You have been successfully unsubscribed.'));
                }
                else
                {
                    // Success and not popup
                    if (!$popup)
                    {
                        $session->addSuccess($this->__('Your subscription has been updated.'));
                    }
                    else
                    {
                        // Only for POPUP
                        $response = array();
                        $response["status"] = "existing";
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                        return;
                    }
                }
            }
            elseif (!$popup)
            {
                // It failed
                $session->addException(new Exception("There was a problem with the subscription."), $this->__('There was a problem with updating the subscription.'));
            }
        }

        // Only for POPUP
        if ($this->getRequest()->isPost() && $popup)
        {
            $response = array();
            $response["status"] = "subscribed";
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }


        // If done via the mini sub
        if (isset($params['mini']) && isset($params['mini']) == 1 && $isNew)
        {
            // Generate the redirect URL
            $url = sprintf("%ssubscribe", Mage::getBaseUrl());
            // We add parameters to the URL so the form will be populated when they access it
            $url .= '?email='.$params['email'].'&key='.md5($params['email'].$this->_hlp()->getApiKey());
        }
        else $url = Mage::getBaseUrl();

        // Redirect
        $this->_redirectUrl($url);
    }


    /**
     * @param $status
     * @param $message
     */
    protected function _putResponse($status,$message)
    {
        $response = array();
        $response['status'] = $status;
        $response['message'] = $message;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

}