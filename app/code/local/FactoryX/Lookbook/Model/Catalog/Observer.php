<?php

/**
 * Class FactoryX_Lookbook_Model_Catalog_Observer
 */
class FactoryX_Lookbook_Model_Catalog_Observer extends Mage_Catalog_Model_Observer
{
    /**
     * Adds catalog categories to top menu
     *
     * @param Varien_Event_Observer $observer
     */
    public function addCatalogToTopmenuItems(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->addCacheTag(Mage_Catalog_Model_Category::CACHE_TAG);
        $this->_addLookbooksToMenu("before",$observer->getMenu(), $block);
        $this->_addCategoriesToMenu(
            Mage::helper('catalog/category')->getStoreCategories(), $observer->getMenu(), $block, true
        );
        $this->_addLookbooksToMenu("after",$observer->getMenu(), $block);
    }

    /**
     * Recursively adds categories to top menu
     *
     * @param Varien_Data_Tree_Node_Collection|array $categories
     * @param Varien_Data_Tree_Node $parentCategoryNode
     * @param Mage_Page_Block_Html_Topmenu $menuBlock
     * @param bool $addTags
     */
    protected function _addCategoriesToMenu($categories, $parentCategoryNode, $menuBlock, $addTags = false)
    {
        $categoryModel = Mage::getModel('catalog/category');
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $nodeId = 'category-node-' . $category->getId();

            $categoryModel->setId($category->getId());
            if ($addTags) {
                $menuBlock->addModelTags($categoryModel);
            }

            $tree = $parentCategoryNode->getTree();
            $categoryData = array(
                'name' => $category->getName(),
                'id' => $nodeId,
                'url' => Mage::helper('catalog/category')->getCategoryUrl($category),
                'is_active' => $this->_isActiveMenuCategory($category)
            );
            $categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            // Retrieve the lookbooks associated to the category
            $lookbooks = Mage::getModel('lookbook/lookbook')
                ->getCollection()
                ->addStatusFilter(1)
                ->addIncludeInNavFilter("category")
                ->addNavCategoryFilter($category->getId())
                ->addStoreFilter()
                ->addAttributeToSort('sort_order', 'asc');

            // Add them to the tree
            foreach($lookbooks as $lookbook)
            {
                $nodeId = 'lookbook-node-' . $lookbook->getIdentifier();

                $lookbookData = array(
                    'name' => $lookbook->getTitle(),
                    'id' => $nodeId,
                    'url' => Mage::getUrl($lookbook->getIdentifier()),
                    'is_active' => $this->_isActiveMenuLookbook($lookbook)
                );
                $lookbookNode = new Varien_Data_Tree_Node($lookbookData, 'id', $tree, $categoryNode);
                $categoryNode->addChild($lookbookNode);
            }

            $flatHelper = Mage::helper('catalog/category_flat');
            if ($flatHelper->isEnabled() && $flatHelper->isBuilt(true)) {
                $subcategories = (array)$category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }

            $this->_addCategoriesToMenu($subcategories, $categoryNode, $menuBlock, $addTags);
        }
    }

    /**
     * @param $position
     * @param $parentCategoryNode
     * @param $menuBlock
     */
    protected function _addLookbooksToMenu($position, $parentCategoryNode, $menuBlock)
    {
        // Retrieve the lookbooks based on their nav position
        $lookbooks = Mage::getModel('lookbook/lookbook')
            ->getCollection()
            ->addStatusFilter(1)
            ->addIncludeInNavFilter($position)
            ->addStoreFilter()
            ->addAttributeToSort('sort_order', 'asc');

        if ($lookbooks->getSize() > 1)
        {
            $nodeId = 'lookbook-node-parent-' . $position;

            $tree = $parentCategoryNode->getTree();
            $lookbookData = array(
                'name' => Mage::helper('lookbook')->__('Collections'),
                'id' => $nodeId,
                'url' => Mage::getUrl($lookbooks->getFirstItem()->getIdentifier()),
                'is_active' => $this->_isActiveTopMenuLookbook($lookbooks->getFirstItem())
            );

            $parentNode = new Varien_Data_Tree_Node($lookbookData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($parentNode);

            foreach ($lookbooks as $lookbook)
            {
                $nodeId = 'lookbook-node-' . $lookbook->getIdentifier();

                $tree = $parentNode->getTree();
                $lookbookData = array(
                    'name' => $lookbook->getTitle(),
                    'id' => $nodeId,
                    'url' => Mage::getUrl($lookbook->getIdentifier()),
                    'is_active' => $this->_isActiveMenuLookbook($lookbook)
                );
                $lookbookNode = new Varien_Data_Tree_Node($lookbookData, 'id', $tree, $parentNode);
                $parentNode->addChild($lookbookNode);
            }

        }
        elseif($lookbooks->getSize())
        {
            foreach ($lookbooks as $lookbook)
            {
                $nodeId = 'lookbook-node-' . $lookbook->getIdentifier();

                $tree = $parentCategoryNode->getTree();
                $lookbookData = array(
                    'name' => $lookbook->getTitle(),
                    'id' => $nodeId,
                    'url' => Mage::getUrl($lookbook->getIdentifier()),
                    'is_active' => $this->_isActiveMenuLookbook($lookbook)
                );
                $lookbookNode = new Varien_Data_Tree_Node($lookbookData, 'id', $tree, $parentCategoryNode);
                $parentCategoryNode->addChild($lookbookNode);
            }
        }
    }

    /**
     * @param $lookbook
     * @return bool
     */
    public function _isActiveMenuLookbook($lookbook)
    {
        return (Mage::helper('core/url')->getCurrentUrl() == Mage::getUrl($lookbook->getIdentifier()));
    }

    /**
     * @return bool
     */
    public function _isActiveTopMenuLookbook()
    {
        // Get the URL path without domain
        $urlString = Mage::helper('core/url')->getCurrentUrl();
        $url = Mage::getSingleton('core/url')->parseUrl($urlString);
        $path = str_replace('/','',$url->getPath());

        // Find the target path of the current URL
        $existingUrlRewrite = Mage::getModel('core/url_rewrite')->loadByRequestPath($path);

        // Check if current page is a lookbook page
        return (strpos($existingUrlRewrite->getTargetPath(),'lookbook/index/view') !== false) ? true : false;
    }
}