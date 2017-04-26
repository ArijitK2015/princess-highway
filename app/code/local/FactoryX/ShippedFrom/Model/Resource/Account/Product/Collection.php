<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Account_Product_Collection
 */
class FactoryX_ShippedFrom_Model_Resource_Account_Product_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('shippedfrom/account_product');
    }

    /**
     * @return $this
     */
    public function addAccountsData()
    {
        $this->getSelect()->join(
            array(
                'account' => 'fx_shippedfrom_account'
            ),
            'account.account_id = main_table.associated_account',
            array('account.name')
        );

        return $this;
    }
}