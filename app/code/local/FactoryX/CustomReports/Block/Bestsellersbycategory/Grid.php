<?php
class FactoryX_CustomReports_Block_Bestsellersbycategory_Grid extends AW_Advancedreports_Block_Advanced_Bestsellers_Grid
{

    public function __construct()
    {
        Mage_Adminhtml_Block_Widget_Grid::__construct();
        $this->setId('bestsellersbycategoryReportGrid');
    }
	
    protected function _prepareCollection()
    {
		// Get the session
		$session = Mage::getSingleton('core/session');
		
		// Dates for one week
		$store = Mage_Core_Model_App::ADMIN_STORE_ID;
		$timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
		date_default_timezone_set($timezone);
		
		// Automatic -30 days if no dates provided
		if ($session->getFrom())
		{
			$sDate = $session->getFrom();
		}
        else
		{
			$sDate = date('Y-m-d 00:00:00',
				Mage::getModel('core/date')->timestamp(strtotime('-30 days'))
			);
		}
		
		if ($session->getTo())
		{
			$eDate = $session->getTo();
		}
        else
		{
			$eDate = date('Y-m-d 23:59:59', 
				Mage::getModel('core/date')->timestamp(time())
			);
		}
		// Get the bestsellers product using aheadWorks collection
		
		/** @var AW_Advancedreports_Model_Mysql4_Collection_Bestsellers $collection  */
        $bestSellers = Mage::getResourceModel('advancedreports/collection_bestsellers');
		
		$this->setCollection($bestSellers);

        $this->getCollection()->setDateFilter($sDate, $eDate)->setState();

        $storeIds = $this->getStoreIds();
        if (count($storeIds)) {
            $this->setStoreFilter($storeIds);
        }

        $this->addOrderItems();
		
		// Remove the aheadWorks limitation so we get all the bestsellers to be able to create accurate data
		$this->getCollection()->getSelect()->reset(Zend_Db_Select::LIMIT_COUNT);
		
		//echo $this->getCollection()->getSelect();
        
		// Array that will contain the data
		$arrayBestSellers = array();
		foreach ($this->getCollection() as $productSold)
		{
			// Get Product ID
			$id = $productSold->getData('product_id');
			
			// Get Sold Quantity and Total
			$sumQty = $productSold->getData('sum_qty');
			$sumTotal = $productSold->getData('sum_total');
			
			// Load the potential parent product ids
			$parentProduct = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
			
			// If a product is an associated product
			if (!empty($parentProduct) && isset($parentProduct[0]))
			{
				// Load the parent configurable product
				$product = Mage::getModel('catalog/product')->load($parentProduct[0]);
			}
			else
			{	
				// Load the simple product
				$product = Mage::getModel('catalog/product')->load($id);
			}
			
			// Get all categories of this product
			$categories = $product->getCategoryCollection();
			// Export this collection to array so we could iterate on it's elements
			$categories = $categories->exportToArray();
			// Get categories names
			foreach($categories as $category)
			{
				// Get Category ID
				$categoryID = $category['entity_id'];
				// Load the category
				$categoryLoaded = Mage::getModel('catalog/category')->load($categoryID);
				// Get Category Name
				$categoryName = $categoryLoaded->getName();
				
				// If category already in the array, we add data
				if (array_key_exists($categoryID, $arrayBestSellers))
				{
					// We update the ordered quantity
					$arrayBestSellers[$categoryID]['ordered_qty'] += $sumQty;
					
					// We udpate the ordered total
					$arrayBestSellers[$categoryID]['ordered_total'] += $sumTotal;
				}
				else
				{
					// For the categories called 'ALL' we need to add the parent category name 
					if (strtolower($categoryName)=='all')
					{
						// Get the parent category Name
						$parentCategoryName = Mage::getModel('catalog/category')->load($categoryLoaded->getParentId())->getName();
						// Add the parent category name 
						$categoryName = $parentCategoryName . " > " . $categoryName;
					}
					
					// Else we create a new entry with the data
					$arrayBestSellers[$categoryID] = array(
						'name'			=>	$categoryName,
						'ordered_qty'	=>	$sumQty,
						'ordered_total'	=>	$sumTotal
					);
				}
			}
		}
		
		// Obtain a list of columns to sort the array using subkeys
		$total = array();
		$qty = array();
		foreach ($arrayBestSellers as $key => $row) {
			$total[$key]  = $row['ordered_total'];
			$qty[$key] = $row['ordered_qty'];
		}

		// Sort the data with qty ascending, views descending
		// Add $arrayBestSellers as the last parameter, to sort by the common key
		array_multisort($total, SORT_DESC, $qty, SORT_DESC, $arrayBestSellers);
		
		// Convert the array to a collection
		$collection = new Varien_Data_Collection();
		foreach($arrayBestSellers as $category){
			$rowObj = new Varien_Object();
			$rowObj->setData($category);
			$collection->addItem($rowObj);
		}
		
        $this->setCollection($collection);
		
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
         $this->addColumn('name', array(
            'header'    => Mage::helper('reports')->__('Category Name'),
			'width'     => '50',
            'index'     => 'name'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    => Mage::helper('reports')->__('Ordered Quantity'),
            'width'     => '150',
            'index'     => 'ordered_qty',
        ));

        $this->addColumn('ordered_total', array(
            'header'    => Mage::helper('reports')->__('Ordered Total'),
            'width'     => '150',
            'index'     => 'ordered_total',
        ));

        $this->addExportType('*/*/exportBestsellersbycategoryCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportBestsellersbycategoryExcel', Mage::helper('reports')->__('Excel'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

}