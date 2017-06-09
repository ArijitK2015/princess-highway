<?php

/**
 * Class FactoryX_CouponValidation_Model_Resource_Log
 */
class FactoryX_CouponValidation_Model_Resource_Log extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('couponvalidation/log', 'log_id');
    }

    public function clean(Mage_Log_Model_Log $object)
    {
        $cleanTime = $object->getLogCleanTime();

        $this->_clean($cleanTime);

        return $this;
    }

    protected function _clean($time)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $timeLimit = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp() - $time);

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    array('log_table' => $this->getTable('couponvalidation/log')),
                    array('log_id' => 'log_table.log_id'))
                ->where('log_table.added < ?', $timeLimit)
                ->limit(100);

            $logIds = $readAdapter->fetchCol($select);

            if (!$logIds) {
                break;
            }

            $condition = array('log_id IN (?)' => $logIds);

            // remove logs
            $writeAdapter->delete($this->getTable('couponvalidation/log'), $condition);
        }

        return $this;
    }

}