<?php

/**
 * Class FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection
 */
class FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('shippedfrom/shipping_queue');
    }

    /**
     * @param array $data
     * @return $this
     */
    public function massUpdate(array $data)
    {
        $this->getConnection()
            ->update(
                $this->getResource()->getMainTable(),
                $data,
                $this->getResource()->getIdFieldName() . ' IN(' . implode(',', $this->getAllIds()) . ')'
            );

        return $this;
    }
}