<?php

/**
 * Class FactoryX_CategoryBanners_Helper_Data
 */
class FactoryX_CategoryBanners_Helper_Data extends Mage_Core_Helper_Abstract
{

    // Protected log file name
    protected $logFileName = 'factoryx_categorybanners.log';

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
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
     * Get the override flag
     */
    public function overrideOriginalCategoryImg()
    {
        return Mage::getStoreConfigFlag('categorybanners/options/override');
    }

    /**
     * Get the custom css
     */
    public function getCss()
    {
        return Mage::getStoreConfig('categorybanners/options/css');
    }
}