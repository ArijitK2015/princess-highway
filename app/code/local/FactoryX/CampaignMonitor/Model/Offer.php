<?php
/**
values taken from the block below, as there is no model to reference
code/core/Mage/Adminhtml/Block/Promo/Quote/Edit/Tab/Actions.php
*/

class FactoryX_CampaignMonitor_Model_Offer {

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
?>