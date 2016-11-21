<?php
/**
 *
 */

class FactoryX_PickList_Model_Mysql4_Picklist_Log_Job_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    
    protected function _construct() {
        $this->_init('picklist/picklist_log_job');
    }
}