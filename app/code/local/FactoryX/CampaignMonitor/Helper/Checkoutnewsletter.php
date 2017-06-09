<?php
/**
 * Class FactoryX_CampaignMonitor_Helper_Checkoutnewsletter
 */
class FactoryX_CampaignMonitor_Helper_Checkoutnewsletter extends Mage_Core_Helper_Abstract
{
    /**
     * @return bool
     */
    public function isCheckoutNewsletterChecked()
    {
        return Mage::getStoreConfigFlag('newsletter/checkoutnewsletter/checked');
    }

    /**
     * @return bool
     */
    public function isCheckoutNewsletterVisibleGuest()
    {
        return Mage::getStoreConfigFlag('newsletter/checkoutnewsletter/visible_guest');
    }

    /**
     * @return bool
     */
    public function isCheckoutNewsletterVisibleRegister()
    {
        return Mage::getStoreConfigFlag('newsletter/checkoutnewsletter/visible_register');
    }
}