<?php
/**
 */

class FactoryX_ShippedFrom_Model_System_Config_Source_ServiceCodes extends Varien_Object {

    protected $options = array(
        'auto'  => "Automatic [by weight]",
        'PP'    => "PP - Parcel Post",
        'EP'    => "EP - Expres Post",
        'PPS'   => "PPS - Parcel Post <500g",
        'EPS'   => "EPS - Express Post <500g"
    );

    /**
     * toOptionArray
     *
     * @return array $options
     */
    public function toOptionArray() {
        return $this->options;
    }
        
}
