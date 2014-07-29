<?php
/**
 *	Clear Cache before saving category 
 */
class FactoryX_ExtendedCatalog_Model_Catalog_Resource_Category extends Mage_Catalog_Model_Resource_Category
{
    /**
     * Process category data before saving
     * prepare path and increment children count for parent categories
     *
     * @param Varien_Object $object
     * @return Mage_Catalog_Model_Resource_Category
     */
    protected function _beforeSave(Varien_Object $object)
    {
        parent::_beforeSave($object);

        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }
        if ($object->getLevel() === null) {
            $object->setLevel(1);
        }

        if (!$object->getId()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path  = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level);
            if ($level) {
                $object->setParentId($path[$level - 1]);
            }
            $object->setPath($object->getPath() . '/');

            $toUpdateChild = explode('/',$object->getPath());

            $this->_getWriteAdapter()->update(
                $this->getEntityTable(),
                array('children_count'  => new Zend_Db_Expr('children_count+1')),
                array('entity_id IN(?)' => $toUpdateChild)
            );

        }
		
		// Clear Cache
		Mage::app()->cleanCache(Mage_Catalog_Model_Category::CACHE_TAG . '_' . $object->getId());
		
        return $this;
    }
}
