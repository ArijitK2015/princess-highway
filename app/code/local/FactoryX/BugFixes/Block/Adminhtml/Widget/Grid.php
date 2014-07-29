<?php
/**
 *	Fix _addColumnFilterToCollection to search for null
 *	Category modifications for product position feature
 */
class FactoryX_BugFixes_Block_Adminhtml_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected function _prepareLayout()
    {
        $this->setChild('export_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Export'),
                    'onclick'   => $this->getJsObjectName().'.doExport()',
                    'class'   => 'task'
                ))
        );
        
        // Only for categories Mage_Catalog_Model_Category
        $category = $this->getCategory();
        if ($category && $category->getId()) 
		{
        	$category = Mage::getModel('catalog/category')->load($category->getId());
	        $products = $category->getProductsPosition();
	        $productsJson = '{}';
	        if (!empty($products)) {
	            $productsJson = Mage::helper('core')->jsonEncode($products);
	            $productsJson = str_replace("\"", "'", $productsJson);
	        }
			
	        $this->setChild('position_button',
	            $this->getLayout()->createBlock('adminhtml/widget_button')
	                ->setData(array(
	                    'label'     => Mage::helper('adminhtml')->__('Set Position'),
	                    'onclick'   => sprintf("setPosition(\$H(%s))", $productsJson),
	                ))
	        );
        }
        
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Reset Filter'),
                    'onclick'   => $this->getJsObjectName().'.resetFilter()',
                ))
        );
        $this->setChild('search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Search'),
                    'onclick'   => $this->getJsObjectName().'.doFilter()',
                    'class'   => 'task'
                ))
        );
        return Mage_Adminhtml_Block_Widget::_prepareLayout();
    }
	
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            }
            else {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {                	 
                	// Treat NULLS differently
                	if (in_array('NULL', array_values($cond), true)) {
                		$this->getCollection()->addFieldToFilter($field, array('null' => true));
                    }
                    else {
                    	$this->getCollection()->addFieldToFilter($field, $cond);
                	}
                }
            }
        }
        return $this;
    }
}
