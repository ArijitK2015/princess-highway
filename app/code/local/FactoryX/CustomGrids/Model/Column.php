<?php

/**
 * Class FactoryX_CustomGrids_Model_Column
 */
class FactoryX_CustomGrids_Model_Column extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'customgrids_column';

    protected function _construct() {
        $this->_init('customgrids/column');
    }

}