<?php

class FactoryX_CampaignMonitor_Model_Checkout_Type_Onepage extends FactoryX_CampaignMonitor_Model_Checkout_Type_Onepage_Abstract
{
    public function saveBilling($data, $customerAddressId)
    {
        if (isset($data['is_subscribed']) && !empty($data['is_subscribed']))
		{
            $this->getCheckout()->setCustomerIsSubscribed(1);
        }
        else 
		{
            $this->getCheckout()->setCustomerIsSubscribed(0);
        }
        return parent::saveBilling($data, $customerAddressId);
    }
}
