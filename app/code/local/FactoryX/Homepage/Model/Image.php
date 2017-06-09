<?php

/**
 * Class FactoryX_Homepage_Model_Image
 */
class FactoryX_Homepage_Model_Image extends Mage_Core_Model_Abstract
{
	/**
	 * Constructor for the homepage image model
	 */
    protected function _construct()
    {
        $this->_init('homepage/image', 'image_id');
    }
}