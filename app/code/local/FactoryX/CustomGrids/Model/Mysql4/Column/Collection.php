<?php

/**
 * Class FactoryX_CustomGrids_Model_Mysql4_Column_Collection
 */
class FactoryX_CustomGrids_Model_Mysql4_Column_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('customgrids/column');
    }

    public function filterByModelAndBlock($model,$block)
    {
        $this->getSelect()
            ->where('main_table.collection_model_type = ?', $model)
            ->where('main_table.grid_block_type = ?', $block);
        return $this;
    }

}