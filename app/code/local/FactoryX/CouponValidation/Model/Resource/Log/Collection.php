<?php

/**
 * Class FactoryX_CouponValidation_Model_Resource_Log_Collection
 */
class FactoryX_CouponValidation_Model_Resource_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct()
    {
        parent::_construct();
        $this->_init('couponvalidation/log');
    }

}