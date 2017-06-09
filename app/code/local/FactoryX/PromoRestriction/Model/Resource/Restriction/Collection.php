<?php

/**
 * Class FactoryX_PromoRestriction_Model_Resource_Restriction_Collection
 */
class FactoryX_PromoRestriction_Model_Resource_Restriction_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('promorestriction/restriction');
    }
}