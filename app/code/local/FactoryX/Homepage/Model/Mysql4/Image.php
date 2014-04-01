<?php

class FactoryX_Homepage_Model_Mysql4_Image extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('homepage/image', 'image_id');
    }
}