<?php
class FactoryX_CampaignMonitor_Block_Checkoutnewsletter extends Mage_Checkout_Block_Onepage_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('newsletter/checkout.phtml');
    }

    public function isChecked()
    {
        return (bool) $this->getCheckout()->getCustomerIsSubscribed();
    }
}