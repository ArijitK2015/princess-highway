<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Shipment_Item
 */
class FactoryX_ShippedFrom_Model_Resource_Shipment_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/shipment_item', 'entity_id');
    }
}