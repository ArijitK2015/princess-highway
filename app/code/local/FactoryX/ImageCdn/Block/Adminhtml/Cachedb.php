<?php

/**
 * Class FactoryX_ImageCdn_Block_Adminhtml_Cachedb
 */
class FactoryX_ImageCdn_Block_Adminhtml_Cachedb extends Mage_Adminhtml_Block_Widget_Grid_Container{

    public function __construct()
    {
        $this->_blockGroup = "factoryx_imagecdn";
        $this->_controller = "adminhtml_cachedb";
        $this->_headerText = Mage::helper('imagecdn')->__('FactoryX CDN');
        $this->_addButtonLabel = Mage::helper('imagecdn')->__('Warm Images');
        parent::__construct();
    }
}