<?php
/**
 * iKantam
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade InstagramConnect to newer
 * versions in the future.
 *
 * @category    Ikantam
 * @package     FactoryX_Instagram
 * @copyright   Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FactoryX_Instagram_Block_Catalog_Product_Gallery extends Mage_Core_Block_Template
{
    public function getInstagramGalleryCollection()
    {
    	return Mage::helper('instagram/product')->/*$this->getProduct()->*/getInstagramGalleryImages(Mage::registry('product'));
    }

    /**
     * @return null
     */
    public function getCurrentInstagramImage()
    {
        $imageId = $this->getRequest()->getParam('image');
        $image = null;
        
        if ($imageId) {
            $image = Mage::getModel('instagram/instagramimage')->load($imageId);
        }
        
        return $image;
    }

    /**
     * @return mixed
     */
    public function getInstagramImageUrl()
    {
        return $this->getCurrentInstagramImage()->getStandardResolutionUrl();
    }

    /**
     * @return bool
     */
    public function getPreviousInstagramImage()
    {
        $current = $this->getCurrentInstagramImage();
        if (!$current) {
            return false;
        }
        $previous = false;
        foreach ($this->getInstagramGalleryCollection() as $image) {
            if ($image->getImageId() == $current->getImageId()) {
                return $previous;
            }
            $previous = $image;
        }
        return $previous;
    }

    /**
     * @return bool
     */
    public function getNextInstagramImage()
    {
        $current = $this->getCurrentInstagramImage();
        if (!$current) {
            return false;
        }

        $next = false;
        $currentFind = false;
        foreach ($this->getInstagramGalleryCollection() as $image) {
            if ($currentFind) {
                return $image;
            }
            if ($image->getImageId() == $current->getImageId()) {
                $currentFind = true;
            }
        }
        return $next;
    }

    /**
     * @return bool
     */
    public function getPreviousInstagramImageUrl()
    {
        if ($image = $this->getPreviousInstagramImage()) {
            return $this->getUrl('*/*/*', array('_current'=>true, 'image'=>$image->getImageId()));
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getNextInstagramImageUrl()
    {
        if ($image = $this->getNextInstagramImage()) {
            return $this->getUrl('*/*/*', array('_current'=>true, 'image'=>$image->getImageId()));
        }
        return false;
    }
}
