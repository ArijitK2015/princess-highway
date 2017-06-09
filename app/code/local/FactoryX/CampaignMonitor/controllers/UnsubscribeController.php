<?php
/**
 * Class FactoryX_CampaignMonitor_UnsubscribeController
 * Frontend controller that handles the unsubscription
 */
class FactoryX_CampaignMonitor_UnsubscribeController extends Mage_Core_Controller_Front_Action
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
     * Unsubscribe action
     * Path: /campaignmonitor/unsubscribe/index?email=emailToUnsubscribe
     */
    public function indexAction()
    {
        try
        {
            // Redirect Url
            $redirectUrl = $this->_hlp()->getRedirectUrl();

            // Get the email
            if(isset($_GET['email']))
            {
                $email = $_GET['email'];

                // Check if subscribed in CM
                $state = $this->_hlpCM()->getCMStatus($email);

                // If we are subscribed in CM, unsubscribe from CM
                if ($state == 2)
                {
                    // Campaign monitor unsubscription
                    $result = $this->_hlpCM()->unsubscribe($email);
                    // If it worked
                    if($result)
                    {
                        // Unsubscribe from Magento
                        $this->_hlp()->mageUnsubscribe($email);
                        Mage::getSingleton('customer/session')->addSuccess($this->__('You were successfully unsubscribed'));
                    }
                }
                // If we are already unsubscribed from CM
                elseif($state != 2)
                {
                    // Check if subscribe in Magento
                    $subscriberStatus = $this->_hlp()->getMagentoStatus($email);
                    // If not unsubscribed yet
                    if ($subscriberStatus != 3)
                    {
                        // Unsubscribe from Magento
                        $this->_hlp()->mageUnsubscribe($email);
                        Mage::getSingleton('customer/session')->addSuccess($this->__('You were successfully unsubscribed'));

                        // Load unsubscribe custom message
                        if ($block = Mage::getModel('cms/block')->load('unsubscribe-custom-message'))
                        {
                            Mage::getSingleton('customer/session')->addNotice($block->getContent());
                        }
                    }
                    // If already unsubscribed, ask if they want to resubscribe
                    else
                    {
                        Mage::getSingleton('customer/session')->addSuccess($this->__('You have already unsubscribed to our newsletter, click <a href="/subscribe">here</a> to resubscribe'));
                    }
                }
            }
        }
        catch (Exception $e)
        {
            $this->_hlp()->log(sprintf("%s", $e->getMessage()));
            Mage::getSingleton('customer/session')->addError($this->__('There was an error while saving your subscription details'));
        }

        // Redirect
        $this->_redirectUrl($redirectUrl);        
    }
}
