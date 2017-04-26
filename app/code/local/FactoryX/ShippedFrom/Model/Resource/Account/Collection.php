<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Account_Collection
 */
class FactoryX_ShippedFrom_Model_Resource_Account_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('shippedfrom/account');
    }

    /**
     * @return $this
     */
    public function addStoreLocationsValues()
    {
        // add store locations title
        if (Mage::helper('core')->isModuleEnabled('FactoryX_StoreLocator')) {
            $this->getSelect()->joinLeft(
                array(
                    'locations' => 'ustorelocator_location'
                ),
                'locations.location_id = main_table.location_id',
                array('locations.title')
            );
        }

        return $this;
    }
}