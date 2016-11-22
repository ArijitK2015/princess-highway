<?php

/**
 * Class FactoryX_PromoRestriction_Model_Resource_Restriction
 */
class FactoryX_PromoRestriction_Model_Resource_Restriction extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct() {
        $this->_init('promorestriction/restriction', 'restriction_id');
    }

}