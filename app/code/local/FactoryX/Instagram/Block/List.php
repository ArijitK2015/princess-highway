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
class FactoryX_Instagram_Block_List extends Mage_Core_Block_Template
{
    public $_listId;

    /**
     *	Retrieve the current list for the frontend
     */
    public function getCurrentList()
    {
        try
        {
            // Retrieve the ID via URL
            if(!$this->_listId)
            {
                $this->_listId = $this->getRequest()->getParam('id');
            }

            // Retrieve the ID via widget
            if(!$this->_listId)
            {
                $this->_listId = $this->getData('list_id');
            }

            // Load list based on the given id
            $currentList = Mage::getModel('instagram/instagramlist')->load($this->_listId);

            return $currentList;
        }
        catch (Exception $e)
        {
            Mage::helper('instagram')->log($this->__('Exception caught in %s under % with message: %s', __FILE__, __FUNCTION__, $e->getMessage()));
            Mage::getSingleton('customer/session')->addError($this->__('There was a problem loading the list'));
            $this->_redirectReferer();
            return;
        }
    }

    /**
     * @return mixed
     */
    public function showInstagramImages()
    {
        return Mage::helper('instagram')->isEnabled();
    }

    /**
     * Retrieve list of gallery images
     *
     * @param null $limit
     * @return array|Varien_Data_Collection
     */
    public function getInstagramGalleryImages($limit = null)
    {
        $data = array();

        $attributes = array('link', 'caption_text', 'standard_resolution_url');

        if ($this->getCurrentList()->getDisplayLikes())
        {
            $attributes[] = 'likes';
        }

        $tagsCollection = Mage::getModel('instagram/instagramimage')
            ->getCollection()
            ->addFieldToSelect($attributes)
            ->addFilter('is_approved', 1)
            ->addFilter('is_visible', 1)
            ->addListFilter($this->_listId)
            ->setOrder('image_order', 'ASC');

        if ($limit)
        {
            $tagsCollection->setPageSize($limit)->setCurPage(1);
        }

        foreach ($tagsCollection as $image) {
            $tmp = array();
            $tmp['link']               = $image->getData('link');
            $tmp['caption_text']       = $this->_stripCaption($image->getData('caption_text'));
            $tmp['standard_resolution_url'] = $image->getData('standard_resolution_url');
            if ($this->getCurrentList()->getDisplayLikes())
            {
                $tmp['likes'] = $image->getData('likes');
            }
            $data[] = $tmp;
        }
        return json_encode($data);
    }

    /**
     * Strip the caption
     * @param $caption
     * @return mixed|string
     */
    protected function _stripCaption($caption)
    {
        $action = $this->getCurrentList()->getStripCaption();
        switch($action)
        {
            case 'full':
                return $caption;
            case 'text':
                return preg_replace('/#\S+ */', '', $caption);;
            case 'tags':
                preg_match_all("/(#\w+)/", $caption, $tags);
                if ($tags)
                {
                    return implode($tags[0],' ');
                }
                else return '';
            default:
                return $caption;
        }
    }

}
