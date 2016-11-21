<?php

/**
 * Class FactoryX_BirthdayGift_Model_System_Config_Source_SalesRuleType
 */
class FactoryX_BirthdayGift_Model_System_Config_Source_SalesRuleType
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION,
                'label' => Mage::helper('salesrule')->__('Percent of product price discount')
            ),
            array(
                'value' => Mage_SalesRule_Model_Rule::CART_FIXED_ACTION,
                'label' => Mage::helper('salesrule')->__('Fixed amount discount for whole cart')
            )
        );
    }
}