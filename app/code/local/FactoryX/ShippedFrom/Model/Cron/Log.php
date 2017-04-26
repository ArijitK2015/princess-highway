<?php

/**
 * Class FactoryX_ShippedFrom_Model_Cron_Log
 */
class FactoryX_ShippedFrom_Model_Cron_Log extends Mage_Core_Model_Abstract
{
    const XML_LOG_CLEAN_DAYS    = 'shippedfrom/auspost_log/clean_after_day';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/cron_log', 'log_id');
    }

    /**
     * @param $message
     * @return $this
     */
    public function addMessage($message)
    {
        $this->setSummary(($this->getSummary() ? $this->getSummary() . "\n" : "") . $message);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogCleanTime()
    {
        return Mage::getStoreConfig(self::XML_LOG_CLEAN_DAYS) * 60 * 60 * 24;
    }

    /**
     * Clean Logs
     *
     * @return FactoryX_ShippedFrom_Model_Cron_Log
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
    
}