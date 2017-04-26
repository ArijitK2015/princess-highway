<?php

/**
 * Class FactoryX_ShippedFrom_Model_Account
 */
class FactoryX_ShippedFrom_Model_Account extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/account', 'account_id');
    }
}