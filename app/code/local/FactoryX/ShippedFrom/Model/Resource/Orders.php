<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Orders
 */
class FactoryX_ShippedFrom_Model_Resource_Orders extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/orders', 'order_id');
    }
}