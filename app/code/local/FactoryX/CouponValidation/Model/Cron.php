<?php

/**
 * Class FactoryX_CouponValidation_Model_Cron
 */
class FactoryX_CouponValidation_Model_Cron
{
    const COUPONVALIDATION_LOG_CLEAN_ENABLED    = 'couponvalidation/options/log_cleaning';

    public function logClean(Mage_Cron_Model_Schedule $schedule = null)
    {
        if (!Mage::getStoreConfigFlag(self::COUPONVALIDATION_LOG_CLEAN_ENABLED)) {
            return $this;
        }

        Mage::getModel('couponvalidation/log')->clean();

        return $this;
    }
}