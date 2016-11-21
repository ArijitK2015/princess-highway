<?php
/**
 */

class FactoryX_ShippedFrom_Model_System_Config_Source_ArticleTypes extends Varien_Object {

    protected $options = array(
        7       => '07 - Own Packaging',
        16      => '16 - Small Flat Rate Box (up to 1kg)',
        17      => '17 - Medium Flat Rate Box (up to 3kg)',
        19      => '19 - Small Flat Rate Satchel (up to 500g)',
        20      => '20 - Medium Flat Rate Satchel (up to 3kg)',
        21      => '21 - Large Flat Rate Satchel (up to 5kg)'
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
