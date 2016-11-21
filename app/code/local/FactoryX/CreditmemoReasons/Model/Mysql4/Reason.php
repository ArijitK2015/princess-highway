<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why:  
 */ 
class FactoryX_CreditmemoReasons_Model_Mysql4_Reason extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('creditmemoreasons/reason', 'reason_id');
    }

}