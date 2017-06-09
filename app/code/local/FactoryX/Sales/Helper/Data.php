<?php

/**
 * Class FactoryX_Sales_Helper_Data
 */
class FactoryX_Sales_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Module log file name
     * @var string
     */
	protected $logFileName = 'factoryx_sales.log';

    /**
     * Constant for the zero total creditmemo refund flag
     */
    const SALES_CREDITMEMO_ZERO_TOTAL_REFUND = "sales/creditmemo/zero_total_refund";

    /**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}

    /**
     * @return bool
     */
	public function canRefundZeroTotalOrders()
    {
        return Mage::getStoreConfigFlag(self::SALES_CREDITMEMO_ZERO_TOTAL_REFUND);
    }
	
}
