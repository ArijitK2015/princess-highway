<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Cron_Log_Collection
 */
class FactoryX_ShippedFrom_Model_Resource_Cron_Log_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('shippedfrom/cron_log');
    }
}