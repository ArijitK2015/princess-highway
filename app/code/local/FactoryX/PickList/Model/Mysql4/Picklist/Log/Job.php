<?php
/**
 */

class FactoryX_PickList_Model_Mysql4_Picklist_Log_Job extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Resource model initialization
     */
    protected function _construct() {
        $this->_init('picklist/job_log', 'job_id');
    }

    /**
     * Clean up log table.
     * 
     * @param int|null $lifetime Lifetime of entries in days
     * @return FactoryX_PickList_Model_Mysql4_Email_Log
     */
    public function clean($lifetime = null) {
        if (!Mage::helper('picklist')->isLogCleaningEnabled()) {
            return $this;
        }
        if (is_null($lifetime)) {
            $lifetime = Mage::helper('picklist')->getLogLifetimeDays();
        }
        $cleanTime = $this->formatDate(time() - $lifetime * 3600 * 24, false);

        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    $this->getMainTable(),
                    $this->getIdFieldName()
                )
                ->where('log_at < ?', $cleanTime)
                ->order('log_at ASC')
                ->limit(100);

            $logIds = $readAdapter->fetchCol($select);
            
            if (!$logIds) {
                break;
            }

            $condition = array($this->getIdFieldName() . ' IN (?)' => $logIds);

            // remove email log entries
            $writeAdapter->delete($this->getMainTable(), $condition);
        }
        
        return $this;
    }
}