<?php

/**
 * Class FactoryX_Homepage_Model_Mysql4_Homepage
 */
class FactoryX_Homepage_Model_Mysql4_Homepage extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('homepage/homepage', 'homepage_id');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('homepage_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('homepage/store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['homepage_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('homepage/store'), $storeArray);
        } else {
            foreach ((array) $object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['homepage_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('homepage/store'), $storeArray);
            }
        }
        return parent::_afterSave($object);
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     * @return \Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('homepage/store'))
                ->where('homepage_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->query($select)) {
            $storesArray = array();
			while ($row = $data->fetch())
			{
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
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {

        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $select->join(array('cps' => $this->getTable('homepage/store')), $this->getMainTable() . '.homepage_id = `cps`.homepage_id')
                    ->where('`cps`.store_id in (0, ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return \Mage_Core_Model_Resource_Db_Abstract|void
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on homepage delete
        $adapter = $this->_getReadAdapter();
		// 2. Delete homepage/store
        $adapter->delete($this->getTable('homepage/store'), 'homepage_id=' . $object->getId());
		// 2. Delete homepage/image
        $adapter->delete($this->getTable('homepage/image'), 'homepage_id=' . $object->getId());
    }

    /**
     * Save attribute to model
     *
     * @param $model - the model
     * @param $attributes - array of attributes to get and save from model
     * @return $this
     * @throws Exception
     */
    public function saveAttribute($model, $attributes)
    {
        try {
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();
            $condition = $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $model->getId());
            $data      = array();
            foreach ($attributes as $attribute) {
                $value = $model->getData($attribute);
                if (isset($value)) {
                    $data[$attribute] = $value;
                }
            }
            if (!empty($data)) {
                $this->_getWriteAdapter()->update($this->getMainTable(), $data, $condition);
            }
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }
        return $this;
    }
}