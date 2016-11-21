<?php
/**
 */

class FactoryX_PickList_Model_System_Config_Source_Option extends Varien_Object {

    /**
     * @return array
     */
    public function toOptionArray() {
        // ftp|local host|download|(view in browser)
        $options = array(
            "view"        => Mage::helper('picklist')->__('View In Browser'),
            "download"    => Mage::helper('picklist')->__('Direct Download'),
            //"file"      => Mage::helper('picklist')->__('File'),
            //"ftp"         => Mage::helper('picklist')->__('FTP')
        );
        
        return $options;
    }
}
