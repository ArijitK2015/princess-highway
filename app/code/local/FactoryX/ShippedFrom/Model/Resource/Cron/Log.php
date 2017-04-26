<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Cron_Log
 */
class FactoryX_ShippedFrom_Model_Resource_Cron_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/cron_log', 'log_id');
    }

    /**
     * Clean logs
     *
     * @param FactoryX_ShippedFrom_Model_Cron_Log $object
     * @return FactoryX_ShippedFrom_Model_Resource_Cron_Log
     */
    public function clean(FactoryX_ShippedFrom_Model_Cron_Log $object)
    {
        $cleanTime = $object->getLogCleanTime();

        Mage::dispatchEvent(
            'factoryx_shippedfrom_log_clean_before',
            array(
                'log'   => $object
            )
        );

        $this->_cleanDatabaseLogs($cleanTime);

        Mage::dispatchEvent(
            'factoryx_shippedfrom_log_clean_after',
            array(
                'log'   => $object
            )
        );

        return $this;
    }

    /**
     * Clean visitors table
     *
     * @param int $time
     * @return FactoryX_ShippedFrom_Model_Resource_Cron_Log
     */
    protected function _cleanDatabaseLogs($time)
    {
        $timeLimit = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp() - $time);

        Mage::getResourceModel('shippedfrom/cron_log_collection')
            ->addFieldToFilter('created_at', array('lt' => $timeLimit))
            ->walk('delete');

        return $this;
    }
}