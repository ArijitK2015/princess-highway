<?php
class FactoryX_CampaignMonitor_Model_System_Config_Source_Offer {

    /**
     * @return array
     */
    public function toOptionArray() {
        return array(
            Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION => Mage::helper('salesrule')->__('Percent of product price discount'),
            Mage_SalesRule_Model_Rule::CART_FIXED_ACTION => Mage::helper('salesrule')->__('Fixed amount discount for whole cart'),
            /*
            never used
            Mage_SalesRule_Model_Rule::BY_FIXED_ACTION => Mage::helper('salesrule')->__('Fixed amount discount'),
            Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION => Mage::helper('salesrule')->__('Buy X get Y free (discount amount is Y)')
            */
        );
    }
}