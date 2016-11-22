<?php

/**
 * Class FactoryX_CouponValidation_Model_Log
 */
class FactoryX_CouponValidation_Model_Log extends Mage_Core_Model_Abstract
{
    const COUPONVALIDATION_LOG_CLEAN_DAYS    = 'couponvalidation/options/clean_after_day';

    protected function _construct()
    {
        $this->_init('couponvalidation/log', 'log_id');
    }

    public function getLogCleanTime()
    {
        return Mage::getStoreConfig(self::COUPONVALIDATION_LOG_CLEAN_DAYS) * 60 * 60 * 24;
    }

    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }

}