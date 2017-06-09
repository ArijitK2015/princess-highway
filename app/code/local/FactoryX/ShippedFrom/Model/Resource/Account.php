<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Account
 */
class FactoryX_ShippedFrom_Model_Resource_Account extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @var
     */
    protected $_productTable;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/account', 'account_id');

        $this->_productTable = $this->getTable('shippedfrom/account_product');
    }

    /**
     * @param Mage_Core_Model_Abstract $account
     * @return FactoryX_ShippedFrom_Model_Resource_Account
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $account)
    {
        $adapter = $this->_getWriteAdapter();

        $adapter->delete(
            $this->_productTable,
            array('associated_account = ?' => (int) $account->getAccountId())
        );

        return $this;
    }

    /**
     * @param Mage_Core_Model_Abstract $account
     * @return FactoryX_ShippedFrom_Model_Resource_Account
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $account)
    {
        if ($account->getMappingType() == "state") {
            $account->setLocationId(0);
        } else {
            $account->setState("");
        }

        return $this;
    }
}