<?php

class FactoryX_ShippedFrom_Model_Australia_Shipping_Carrier_Australiapost
    extends Fontis_Australia_Model_Shipping_Carrier_Australiapost
{
    /**
     * @param $trackings
     * @return false|Mage_Core_Model_Abstract
     */
    protected function _getTracking($trackings)
    {
       $result = parent::_getTracking($trackings);
       Mage::dispatchEvent('fontis_australia_auspost_tracking_info', array('result' => $result));
       return $result;
    }
}