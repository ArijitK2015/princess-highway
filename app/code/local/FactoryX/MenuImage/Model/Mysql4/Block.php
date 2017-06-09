<?php

/**
 * Class FactoryX_MenuImage_Model_Mysql4_Block
 */
class FactoryX_MenuImage_Model_Mysql4_Block extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('menuimage/block', 'block_id');
    }
}