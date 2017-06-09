<?php
/**
 * Fix Group By Count bug
 * Only affects Magento < 1.9.1.0 and Magento > 1.5.1.0
 * Source: http://ka.lpe.sh/2012/01/05/magento-wrong-count-in-admin-grid-when-using-group-by-clause-overriding-lib-module/)
 */
class FactoryX_BugFixes_Model_Catalog_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Get SQL for get record count
     *
     * @param null $select
     * @param bool $resetLeftJoins
     * @return Varien_Db_Select
     */
    protected function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        if (version_compare(Mage::getVersion(),"1.5.1.0","<=") || version_compare(Mage::getVersion(),"1.9.1.0",">="))
        {
            return parent::_getSelectCountSql($select, $resetLeftJoins);
        }
        else
        {
            $this->_renderFilters();
            $countSelect = (is_null($select)) ?
                $this->_getClearSelect() :
                $this->_buildClearSelect($select);
            // Clear GROUP condition for count method
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->columns('COUNT(DISTINCT e.entity_id)');
            if ($resetLeftJoins) {
                $countSelect->resetJoinLeft();
            }
            return $countSelect;
        }
    }
}
