<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Account_Product
 */
class FactoryX_ShippedFrom_Model_Resource_Account_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/account_product', 'entity_id');
    }
}