<?php

/**
 * Class FactoryX_PromoRestriction_Model_Restriction
 */
class FactoryX_PromoRestriction_Model_Restriction extends Mage_Core_Model_Abstract {

    const RESTRICT_IP       = 'ip';
    const RESTRICT_EMAIL    = 'email';
    const RESTRICT_NONE     = 'none';
    const RESTRICT_STORE    = 'store';

    /**
     *
     */
    protected function _construct() {
        $this->_init('promorestriction/restriction', 'restriction_id');
    }

}