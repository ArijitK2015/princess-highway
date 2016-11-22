<?php
/**
 * iKantam LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the iKantam EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://magento.factoryx.com/store/license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * HouseConfigurator Module to newer versions in the future.
 *
 * @category   Ikantam
 * @package    Ikantam_HouseConfigurator
 * @author     iKantam Team <support@factoryx.com>
 * @copyright  Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license    http://magento.factoryx.com/store/license-agreement  iKantam EULA
 */
class FactoryX_Instagram_Block_Catalog_Category_View_Images extends Mage_Core_Block_Template
{

    protected $_collection = array();

    /**
     * @return bool
     */
    public function showInstagramImages()
    {
        $helper = Mage::helper('instagram');
        return ($helper->isEnabled() && $helper->showImagesOnProductPage() && count($this->getInstagramGalleryImages()) > 0);
    }

    /**
     * Retrieve list of gallery images
     *
     * @return array|Varien_Data_Collection
     */
    public function getInstagramGalleryImages()
    {
        if (!$this->_collection) {
            $this->_collection = Mage::helper('instagram/category')->getInstagramGalleryImages(Mage::registry('current_category'));
        }
        return $this->_collection;
    }

}
