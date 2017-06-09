<?php
/**
 * Index api
 *
 * @package    FactoryX_ExtendedApi
 * @author     Factory X Team <developers@factoryx.com.au>
 */

 class FactoryX_ExtendedApi_Model_Index_Api extends Mage_Api_Model_Resource_Abstract {

    /**
     * Turn index on
     *
     * @param array $codes index codes
     * @return bool
     */
    public function turnIndexOn($codes = null) {
        $retVal = true;
        //Mage::helper('extended_api')->log(sprintf("%s->turn index on: %s", __METHOD__, print_r($codes, true)));
    	Mage::helper('extended_api')->setIndex('on', $codes);
    	return $retVal;
    }

    /**
     * Turn index off
     *
     * @param array $codes index codes
     * @return bool
     */
    public function turnIndexOff($codes = null) {
        $retVal = true;
    	//Mage::helper('extended_api')->log(sprintf("%s->turn index off: %s", __METHOD__, print_r($codes, true)));
        Mage::helper('extended_api')->setIndex('off', $codes);
        return $retVal;
    }

}