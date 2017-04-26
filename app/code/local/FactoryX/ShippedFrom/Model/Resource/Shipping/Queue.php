<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Shipping_Queue
 */
class FactoryX_ShippedFrom_Model_Resource_Shipping_Queue extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/shipping_queue', 'schedule_id');
    }
}