<?php
if ((string)Mage::getConfig()->getModuleConfig('Temando_Temando')->active == 'true'){
    class FactoryX_CampaignMonitor_Model_Checkout_Type_Onepage_Abstract extends Temando_Temando_Model_Checkout_Type_Onepage {}
} else {
    class FactoryX_CampaignMonitor_Model_Checkout_Type_Onepage_Abstract extends Mage_Checkout_Model_Type_Onepage {}
}
