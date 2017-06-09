<?php
/**
 *	Fix the bug "Magento Inventory Rebuild sets Grouped Products out of stock"
 *  Only happens on Magento < 1.8.1.0 and > 1.5.1.0
 * 	Source: http://aaronbonner.io/post/20956645813/magento-inventory-rebuild-sets-grouped-products-out-of
 */
class FactoryX_BugFixes_Model_Catalog_Resource_Product_Status extends Mage_Catalog_Model_Resource_Product_Status
{
    /**
     * Retrieve Product(s) status for store
     * Return array where key is a product_id, value - status
     *
     * @param array|int $productIds
     * @param int $storeId
     * @return array
     */
    public function getProductStatus($productIds, $storeId = null)
    {
        if (version_compare(Mage::getVersion(),"1.5.1.0","<=") || version_compare(Mage::getVersion(),"1.8.1.0",">="))
        {
            return parent::getProductStatus($productIds, $storeId);
        }
        else {
            $statuses = array();

            $attribute = $this->_getProductAttribute('status');
            $attributeTable = $attribute->getBackend()->getTable();
            $adapter = $this->_getReadAdapter();

            if (!is_array($productIds)) {
                $productIds = array($productIds);
            }

            if ($storeId === null || $storeId == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
                $select = $adapter->select()
                    ->from($attributeTable, array('entity_id', 'value'))
                    ->where('entity_id IN (?)', $productIds)
                    ->where('attribute_id = ?', $attribute->getAttributeId())
                    ->where('store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);

                $rows = $adapter->fetchPairs($select);
            } else {
                $valueCheckSql = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');

                $select = $adapter->select()
                    ->from(
                        array('t1' => $attributeTable),
                        array('entity_id' => 't1.entity_id', 'value' => $valueCheckSql)
                    )
                    ->joinLeft(
                        array('t2' => $attributeTable),
                        't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = ' . (int)$storeId,
                        array('t1.entity_id')
                    )
                    ->where('t1.store_id = ?', Mage_Core_Model_App::ADMIN_STORE_ID)
                    ->where('t1.attribute_id = ?', $attribute->getAttributeId())
                    ->where('t1.entity_id IN(?)', $productIds);
                $rows = $adapter->fetchPairs($select);
            }

            foreach ($productIds as $productId) {
                if (isset($rows[$productId])) {
                    $statuses[$productId] = $rows[$productId];
                } else {
                    $statuses[$productId] = -1;
                }
            }

            return $statuses;
        }
    }
}