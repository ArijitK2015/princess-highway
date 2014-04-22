<?php

class FactoryX_Lookbook_Model_Mysql4_Lookbook extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('lookbook/lookbook', 'lookbook_id');
    }

	protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('lookbook_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('lookbook/store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['lookbook_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('lookbook/store'), $storeArray);
        } else {
            foreach ((array) $object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['lookbook_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('lookbook/store'), $storeArray);
            }
        }
        return parent::_afterSave($object);
    }
	
	/**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('lookbook/store'))
                ->where('lookbook_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }
	
	/**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {

        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $select->join(array('cps' => $this->getTable('lookbook/store')), $this->getMainTable() . '.lookbook_id = `cps`.lookbook_id')
                    ->where('`cps`.store_id in (0, ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }
	
	protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on lookbook delete
        $adapter = $this->_getReadAdapter();
		// 1. Delete rewrite rules
		try
		{
			// If multistore
			if (!Mage::app()->isSingleStoreMode())
			{
				$select = $this->_getReadAdapter()->select()
						->from($this->getTable('lookbook/store'))
						->where('lookbook_id = ?', $object->getId());

				if ($data = $this->_getReadAdapter()->fetchAll($select)) 
				{
					foreach ($data as $row) 
					{
						$affectedStoreId = $row['store_id'];
						$idPath = $object->getIdentifier()."_".$affectedStoreId;
						$urlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($idPath);
						$urlRewrite->delete();
					}
				}
			}
			// If single store
			else
			{
				$urlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($object->getIdentifier());
				$urlRewrite->delete();
			}
		}
		catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		// 2. Delete lookbook/store
        $adapter->delete($this->getTable('lookbook/store'), 'lookbook_id=' . $object->getId());
    }
}