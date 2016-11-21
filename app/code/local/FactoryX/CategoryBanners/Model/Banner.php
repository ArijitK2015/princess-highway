<?php

/**
 * Class FactoryX_CategoryBanners_Model_Banner
 */
class FactoryX_CategoryBanners_Model_Banner extends Mage_Core_Model_Abstract
{
	/**
	 *	Constructor for the banner model
	 */
    protected function _construct()
    {
        $this->_init('categorybanners/banner', 'banner_id');
    }
}