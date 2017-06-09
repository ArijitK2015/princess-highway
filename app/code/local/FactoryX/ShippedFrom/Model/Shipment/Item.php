<?php

/**
 * Class FactoryX_ShippedFrom_Model_Shipment_Item
 */
class FactoryX_ShippedFrom_Model_Shipment_Item extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/shipment_item', 'entity_id');
    }
    
}