<?php

/**
 * Class FactoryX_Lookbook_Helper_Data
 */
class FactoryX_Lookbook_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 * @var string
     */
	protected $logFileName = 'factoryx_lookbook.log';

    protected $pricePattern = "\\$?\d+(\.\d+)?";

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data)
	{
		Mage::log($data, null, $this->logFileName);
	}

	/**
	 * calculateLookDimensions
	 *
	 * calculates the look dimensions
	 *
	 * @param FactoryX_Lookbook_Model_Lookbook $lookbook the lookbook
	 * @return array $dimensions array('width' => width, 'height' => height)
	 */
	public function calculateLookDimensions($lookbook) {

		$lookbookType = $lookbook->getLookbookType();
		if ($lookbookType == "category")
		{
			if ($lookbookProducts = $lookbook->getLookbookProducts())
			{
				// Get first lookbook product
				$_firstProduct = $lookbookProducts->getFirstItem();
				// Get image without using the cache (as we need the original size)
				$_image = sprintf("%s/catalog/product/%s", Mage::getBaseDir('media'), $_firstProduct->getImage() );
				if(!file_exists($_image)) $_image = sprintf("%s/catalog/product/%s", Mage::getBaseUrl('media'), $_firstProduct->getImage() );

				// Retrieve attributes of the image (may not exist)
				try {
					list($width, $height) = getimagesize($_image);
				}
				catch(Exception $ex) {
					// TODO: use dev default
					$width = 0;
					$height = 0;
				}
				$dimensions['width'] = $width;
				$dimensions['height'] = $height;
				$dimensions['ratio'] = 0;
				if ($height != 0 && $width != 0) {
					$dimensions['ratio'] = $height / $width;
				}
			}
			else {
				$dimensions['ratio'] = 0;
				$dimensions['width'] = 0;
				$dimensions['height'] = 0;
				return $dimensions;
			}
		}
		else {
			// Get lookbook images
			$_images = $lookbook->getGallery();
			// Get first image
			$_firstImage = $_images[0];
			// Generate its real path
			$imagePath = sprintf("%s/lookbook%s", Mage::getBaseDir('media'), $_firstImage['file']);
			try {
				if(!file_exists($imagePath)){
					$imagePath = sprintf("%slookbook%s", Mage::getBaseUrl('media'), $_firstImage['file']);
				}
			// Retrieve attributes of the image

				list($width, $height) = getimagesize($imagePath);
			}
			catch(Exception $ex) {
				// TODO: use dev default
				$width = 0;
				$height = 0;
			}
			$dimensions['width'] = $width;
			$dimensions['height'] = $height;
			$dimensions['ratio'] = 0;
			if ($height != 0 && $width != 0) {
				$dimensions['ratio'] = $height / $width;
			}
		}
		return $dimensions;
	}

	/**
	 * List all categories in an array
	 *
	 * Array(
	 *   "1|Root Category" => Array(
	 *     "2|Next Cat" => Array()
	 *     "3|Another Cat" => Array()
	 *     ...
	 *   )
	 *   ...
	 * )
	 */
    function  getCategoriesArray($catId, &$cats) {
        $category = Mage::getModel('catalog/category')->load($catId);
        $name = $category->getName();
        if (empty($name)) {
            $name = "root";
        }
        $name = sprintf("%s|%s", $catId, $name);

        //$category->getIncludeInMenu();
        $cats[$name] = array();

        if ($category->hasChildren()) {
            //$categories = Mage::getModel('catalog/category')->getCategories($category->getId());
            $categories = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('*')
                //->addAttributeToFilter('is_active', 1)
                //->addAttributeToFilter('include_in_menu', 0)
                ->addAttributeToFilter('parent_id', $category->getId())
                ->addAttributeToSort('position');

            // Varien_Data_Tree_Node
            foreach($categories as $category) {
                $this->getCategoriesArray($category->getId(), $cats[$name]);
            }
        }
        return;
    }

	/**
	 * @param $_product
	 * @return array
	 */
	public function getChildProductsLinkOnly($_product)
	{
		$childProductsLinks = array();

		$bundleOptions = $_product->getTypeInstance(true)->getChildrenIds($_product->getId(), false);

		foreach ($bundleOptions as $bundleOption)
		{
			// We get the first simple products of the option using reset
			$simpleProductId = reset($bundleOption);

			// Retrieve its parent configurable product
			$parentId = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($simpleProductId);

			// Load the parent
			$collection = Mage::getResourceModel('catalog/product_collection')
				->addFieldToFilter('entity_id', array(reset($parentId)))
				->addAttributeToSelect(array('status','visibility','product_url','name','price'))
				->setPageSize(1);

			$childProduct = $collection->getFirstItem();

			// Product enabled + product visible + product configurable = product link
			if ($childProduct->getStatus() == 1 && $childProduct->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
			{
				$childProductsLinks[] = array( 'link' => $childProduct->getProductUrl(), 'name' => $childProduct->getName(), 'price' =>  Mage::helper('core')->currency($childProduct->getPrice(), true, false));
			}
		}

		return $childProductsLinks;
	}
	
	/**
	 * strip price
	 */
	public function stripPrice($str) {
        if (preg_match(sprintf("/%s/", $this->pricePattern), $str)) {
            $str = preg_replace(sprintf("/%s/", $this->pricePattern), "", $str);
            $str = trim($str);
	    }
	    return $str;
	}	

	/**
	 * @param $_product
	 * @return array
	 */
	public function getChildProductsLink($_product, $stripPrice = false) {
        //Mage::helper('lookbook')->log(sprintf("%s->product: %s", __METHOD__, $_product->getName()));
		$childProductsLinks = array();

        $optionCol = $_product->getTypeInstance(true)->getOptionsCollection($_product);
        $selectionCol = $_product->getTypeInstance(true)->getSelectionsCollection(
            $_product->getTypeInstance(true)->getOptionsIds($_product), $_product
        );
        $optionCol->appendSelections($selectionCol);

        foreach ($optionCol as $option) {
            $position = $option->getPosition();
            $name = $option->getData('default_title');
            //Mage::helper('lookbook')->log(sprintf("%s->%d=%s", __METHOD__, $position, $name) );

            // has availble options
            if (is_array($option->getSelections()) && count($option->getSelections())) {

                $selections = $option->getSelections();
                //Mage::helper('lookbook')->log(sprintf("%s->%s|%s|%s|%s", __METHOD__, $name, count($selections), get_class($selections[0])) );

    			// get the first simple Mage_Catalog_Model_Product of the option
    			$simpleProductId = $selections[0]->getId();

    			// Retrieve its parent configurable product
    			$parentId = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($simpleProductId);

    			// Load the parent
    			$collection = Mage::getResourceModel('catalog/product_collection')
    				->addFieldToFilter('entity_id', array(reset($parentId)))
    				->addAttributeToSelect(array('status','visibility','product_url','name','price'))
    				->setPageSize(1);

			    $childProduct = $collection->getFirstItem();

    			// Product enabled + product visible + product configurable = product link
    			if ($childProduct->getStatus() == 1 && $childProduct->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) {
    			    //$price = Mage::helper('core')->currency($childProduct->getPrice(), true, false);
    			    $price = Mage::app()->getStore()->getCurrentCurrency()->formatPrecision($childProduct->getPrice(), 0, array(), true, false);
    				$childProductsLinks[] = array(
    				    'link'  => $childProduct->getProductUrl(),
    				    'name'  => $childProduct->getName(),
    				    'price' => $price
                    );
    			}
    			else {
    			    if ($stripPrice) {
    			        $name = $this->stripPrice($name);
    			    }
    			    $childProductsLinks[] = array(
    			        'name' => $name
    			    );
    			}
    		}
    		else {
			    if ($stripPrice) {
			        $name = $this->stripPrice($name);
			    }
			    //Mage::helper('lookbook')->log(sprintf("%s->name: %s", __METHOD__, $name));
			    $childProductsLinks[] = array(
			        'name' => $name
			    );
    		}
		}
		return $childProductsLinks;
	}

	/**
	 *    List files from a directory in an usable way
	 * @param $directry
	 * @param $subdir
	 * @param $slider
	 * @return array
	 */
	public function dirFiles($directry,$subdir,$slider = false)
	{
		// Open Directory
		$dir = dir(str_replace("\\","/",$directry.DS.$subdir));

		// Declare array
		$filesall = array();

		// Reads Directory
		while (false!== ($file = $dir->read()))
		{
			// Gets the File Extension and the Layout Name
			list($layoutName,$extension) = explode('.', $file, 2);
			// Extensions Allowed
			if($extension == "png" || $extension == "jpg" || $extension == "gif" |$extension == "jpeg")
			{
				// If no slider / If slider
				if ((!$slider && strpos($file,'slider') === false) || ($slider && strpos($file,'slider') !== false))
				{
					// Store in Array
					$filesall[] = array('value' => $subdir."/".$layoutName,'image' => $subdir."/".$layoutName.'.'.$extension);
				}
			}
		}

		// Close Directory
		$dir->close();
		// Sorts the Array
		sort($filesall);
		// Return the Array
		return $filesall;
	}

	/**
	 * @param $string
	 * @return string
	 */
	public function stripNav($string)
	{
		return str_replace("nav/","",$string);
	}

	/**
	 * @param $string
	 * @return string
	 */
	public function stripPag($string)
	{
		return str_replace("pag/","",$string);
	}

	/**
	 * getSelectableCategories
	 *
	 * returns a flattened array of categories
	 *
	 * @returns Array(
	 *   1 => "cat",
	 *   2 => "-> cat 2",
	 *   3 => "-> cat 3"
	 * )
	 */
	public function getSelectableCategories() {

        $website = Mage::getModel('core/website')->load(1, 'is_default');
        $storeId = $website->getDefaultGroup()->getDefaultStoreId();

        $rootCatId = Mage::app()->getStore($storeId)->getRootCategoryId();
        //Mage::helper('lookbook')->log(sprintf("%d: %d", $storeId, $rootCatId));

        $cats = array();
        $this->getCategoriesArray($rootCatId, $cats);

        //Mage::helper('lookbook')->log(sprintf("cats: %s", print_r($cats, true)) );

        $flatCats = array();
        $this->flattenTree($cats, 0, $flatCats);
        return $flatCats;
    }

    /**
     * flattenTree
     *
            'label' => $category['name'],
            'level' => $category['level'],
            'value' => $categoryId


     * @param array $arr array of cats
     * @param int $level the level of the cat 0 ... N
     * @return (type) (name)
     */
    function flattenTree($arr, $level, &$cats) {
        foreach ($arr as $name => $subs) {
            $values = preg_split("/\|/", $name, 2);
            //Mage::helper('lookbook')->log(sprintf("%s->name=%s", __METHOD__, print_r($values, true)) );
			$cats[] = array(
				'value' => $values[0],
				'label' => $values[1],
				'level' => $level
			);
            if (is_array($subs) && count($subs)) {
                $this->flattenTree($subs, $level+1, $cats);
            }
        }
    }

	/**
	 * Get the next lookbook id
	 * @return mixed
     */
	public function getNextId()
	{
		$lookbook_entity_table = Mage::getSingleton('core/resource')->getTableName('lookbook/lookbook');
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_read');
		$result = $connection->showTableStatus($lookbook_entity_table);
		$next_lookbook_id = $result['Auto_increment'];
		return $next_lookbook_id;
	}

	protected function _isNoFlashUploader()
	{
		return class_exists("Mage_Uploader_Block_Abstract");
	}

	public function getFlowMin()
	{
		return $this->_isNoFlashUploader() ? "lib/uploader/flow.min.js" : null;
	}

	public function getFustyFlow()
	{
		return $this->_isNoFlashUploader() ? "lib/uploader/fusty-flow.js" : null;
	}

	public function getFustyFlowFactory()
	{
		return $this->_isNoFlashUploader() ? "lib/uploader/fusty-flow-factory.js" : null;
	}

	public function getAdminhtmlUploaderInstance()
	{
		return $this->_isNoFlashUploader() ? "mage/adminhtml/uploader/instance.js" : null;
	}

}