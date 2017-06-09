<?php
/**
 * Add a new mode
 */


class FactoryX_ExtendedCatalog_Model_Adminhtml_System_Config_Source_Catalog_ListMode extends Mage_Adminhtml_Model_System_Config_Source_Catalog_ListMode
{
    public function toOptionArray()
    {
        return array(
            //array('value'=>'', 'label'=>''),
            array('value'=>'grid', 'label'=>Mage::helper('adminhtml')->__('Grid Only')),
            array('value'=>'list', 'label'=>Mage::helper('adminhtml')->__('List Only')),
            array('value'=>'gridx5', 'label'=>Mage::helper('adminhtml')->__('Grid x5 Only')),
            array('value'=>'grid-list', 'label'=>Mage::helper('adminhtml')->__('Grid (default) / List')),
            array('value'=>'grid-gridx5', 'label'=>Mage::helper('adminhtml')->__('Grid (default) / Grid x5')),
            array('value'=>'list-grid', 'label'=>Mage::helper('adminhtml')->__('List (default) / Grid')),
            array('value'=>'list-gridx5', 'label'=>Mage::helper('adminhtml')->__('List (default) / Grid x5')),
            array('value'=>'gridx5-list', 'label'=>Mage::helper('adminhtml')->__('Grid x5 (default) / List')),
            array('value'=>'gridx5-grid', 'label'=>Mage::helper('adminhtml')->__('Grid x5 (default) / Grid')),
            array('value'=>'grid-list-gridx5', 'label'=>Mage::helper('adminhtml')->__('Grid (default) / List / Grid x5')),
            array('value'=>'list-grid-gridx5', 'label'=>Mage::helper('adminhtml')->__('List (default) / Grid / Grid x5')),
            array('value'=>'gridx5-list-grid', 'label'=>Mage::helper('adminhtml')->__('Grid x5 (default) / List / Grid')),
        );
    }
}