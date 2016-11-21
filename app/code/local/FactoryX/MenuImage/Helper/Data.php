<?php

/**
 * Class FactoryX_MenuImage_Helper_Data
 */
class FactoryX_MenuImage_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var int
     * Number of blocks per menu
     */
    protected $_blockCount = 3;

    /**
     *
     */
    public function getBlockCount()
    {
        return $this->_blockCount;
    }

    /**
     * @deprecated since 0.1.0
     * @param $menuId
     * @param string $imageSuffix
     * @return null|string
     */
    public function getMenuImageUrl($menuId,$imageSuffix = "")
    {
        // Strip ID
        $categoryId = str_replace("category-node-","",$menuId);
        // Get image from the category
        $categoryImage = Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('entity_id', array($categoryId))
            ->addAttributeToSelect(array('menu_image'.$imageSuffix))
            ->setPageSize(1)
            ->getFirstItem()
            ->getData('menu_image'.$imageSuffix);

        $url = null;
        if ($categoryImage) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$categoryImage;
        }
        return $url;
    }

    /**
     *	List all categories in an array
     */
    public function getCategoriesArray()
    {
        // Get categories
        $categoriesArray = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('path', 'asc')
            ->load()
            ->toArray();

        $categories = array();

        // Make them an usable array
        foreach ($categoriesArray as $categoryId => $category)
        {
            if (isset($category['name']))
            {
                $categories[] = array(
                    'label' => $category['name'],
                    'level' => $category['level'],
                    'value' => $categoryId
                );
            }
        }

        return $categories;
    }

    /**
     * Generate array to process the menu image blocks easier
     * @param $blocks
     * @return array
     */
    public function generateBlocksArray($blocks)
    {
        $array = array();
        foreach ($blocks as $block)
        {
            if ($block->getType() == 'product')
            {
                $productId = str_replace('product/','',$block->getProductId());

                $collection = Mage::getResourceModel('catalog/product_collection')
                    ->addIdFilter($productId)
                    ->addAttributeToSelect(array('product_url','name','small_image'))
                    ->setPageSize(1);

                /** @var Mage_Catalog_Model_Product $product */
                $product = $collection->getFirstItem();
                $array[$block->getId()]['url'] = $product->getProductUrl();
                $array[$block->getId()]['alt'] = $product->getName();
                $array[$block->getId()]['image'] = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(200);
            }
            elseif ($block->getType() == 'image')
            {
                $array[$block->getId()]['url'] = $block->getLink();
                $array[$block->getId()]['alt'] = $block->getAlt();
                $relMediaPath = substr(Mage::getBaseDir('media'), strlen(Mage::getBaseDir()) );
                $imageUrl = str_replace(DS,"/",sprintf("%s/menuimage%s", $relMediaPath, $block->getUrl()));
                $array[$block->getId()]['image'] = $imageUrl;
            }
        }
        return $array;
    }

    /**
     *    Add blocks data to the menuimage data
     * @param $data
     * @return
     */
    public function addBlocksData($data)
    {
        if (array_key_exists('menuimage_id',$data))
        {
            // Retrieve the menuimage blocks
            $menuimageBlocks = Mage::getModel('menuimage/block')->getCollection()->addMenuimageFilter($data['menuimage_id']);
            // Foreach pictures
            foreach($menuimageBlocks as $menuimageBlock)
            {
                // Retrieve data
                $data['image_'.$menuimageBlock->getIndex()] = $menuimageBlock->getUrl();
                $data['link_'.$menuimageBlock->getIndex()] = $menuimageBlock->getLink();
                $data['alt_'.$menuimageBlock->getIndex()] = $menuimageBlock->getAlt();
                $data['type_'.$menuimageBlock->getIndex()] = $menuimageBlock->getType();
                $data['product_id_'.$menuimageBlock->getIndex()] = $menuimageBlock->getProductId();
                $data['sort_order_'.$menuimageBlock->getIndex()] = $menuimageBlock->getSortOrder();
            }
            return $data;
        }
        else return $data;
    }
}