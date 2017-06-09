<?php
/**
 * Class FactoryX_CampaignMonitor_Block_Checkoutnewsletter
 */
class FactoryX_CampaignMonitor_Block_Checkoutnewsletter extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * @return bool
     */
    public function isChecked()
    {
        return (bool) ($this->getCheckout()->getCustomerIsSubscribed() || Mage::helper('campaignmonitor/checkoutnewsletter')->isCheckoutNewsletterChecked());
    }
}