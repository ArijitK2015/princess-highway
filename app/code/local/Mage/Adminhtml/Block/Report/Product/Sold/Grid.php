<?php
/*
	Add the following columns to the grid:
	- sku
	- season
	- brand
	- colour
	- size
	- special_price
	- price

	http://codemagento.com/2011/03/creating-custom-magento-reports/
	http://magentocoder.jigneshpatel.co.in/create-custom-reports-in-magento-admin/
*/

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

 
/**
 * Report Sold Products Grid Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Sold_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    /**
     * Sub report size
     *
     * @var int
     */
    protected $_subReportSize = 0;

    /**
     * Initialize Grid settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProductsSold');
    }

    /**
     * Prepare collection object for grid
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('reports/product_sold_collection');
        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareColumns()
    {
		$this->addColumn('sku', array(
            'header'    => Mage::helper('reports')->__('sku'),
            'index'     =>'order_items_sku'
        ));     	
    	    	
        $this->addColumn('season', array(
            'header'    => Mage::helper('reports')->__('Season'),
            'index'     => 'season',
            'type'      => 'options',
            'options' 	=> $this->_getAttributeOptions('season')
        ));

        $this->addColumn('brand', array(
            'header'    => Mage::helper('reports')->__('Brand'),
            'index'     => 'brand',
            'type'      => 'options',
            'options' 	=> $this->_getAttributeOptions('brand')
        ));

        $this->addColumn('colour_description', array(
            'header'    => Mage::helper('reports')->__('Colour'),
            'index'     => 'colour_description',
            'type'      => 'options',
            'options' 	=> $this->_getAttributeOptions('colour_description')
        ));

        $this->addColumn('size', array(
            'header'    => Mage::helper('reports')->__('Size'),
            'index'     => 'order_items_sku',
            'renderer'  => 'Mage_Adminhtml_Block_Report_Product_Sold_Renderer_Size'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'order_items_name'
        ));

        $this->addColumn('special_price', array(
            'header'    =>Mage::helper('reports')->__('Sale Price'),
            'width'     =>'120px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'index'     =>'special_price'
        ));

        $this->addColumn('price', array(
            'header'    =>Mage::helper('reports')->__('Price'),
            'width'     =>'120px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'index'     =>'price'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    =>Mage::helper('reports')->__('Quantity Sold'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'ordered_qty',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $this->addExportType('*/*/exportSoldCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSoldExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
    
    public function _getAttributeOptions($attrId) {
	    $options = Mage::getResourceModel('eav/entity_attribute_collection')
	        ->setCodeFilter($attrId)
	        ->getFirstItem();
	    $optionsOptions = $options->getSource()->getAllOptions(false);
	    $optionsArr = array();
	    foreach ($optionsOptions as $option) {
	    	//Mage::log($option['value'] . "=" . $option['label']);
	        $optionsArr[$option['value']] = $option['label'];
	    }
	    return $optionsArr;
		
    }
}
