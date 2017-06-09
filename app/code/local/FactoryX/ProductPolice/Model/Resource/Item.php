<?php
/**
 * Who:  Alvin Nguyen
 * When: 2/10/2014
 * Why:  
 */

class FactoryX_ProductPolice_Model_Resource_Item extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct(){
        $this->_init('factoryx_productpolice/item','item_id');
    }
}