<?php

/**
 * Class FactoryX_AWAdvancedreportsMod_Block_Advanced_Bestsellers
 */
class FactoryX_AWAdvancedreportsMod_Block_Advanced_Bestsellers extends AW_Advancedreports_Block_Advanced_Bestsellers
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        /**** START CUSTOM FACTORY X CODE ****/
        // Filter by sale items button
        $currentParams = $this->getRequest()->getParams();
        if (array_key_exists('filter_nonsale',$currentParams))	unset($currentParams['filter_nonsale']);
        $currentParams['filter_sale'] = true;

        $this->_addButtonLabel = Mage::helper('advancedreports')->__('Filter by Sale Items');
        $this->_addButton('filter_sale', array(
            'label'     => Mage::helper('advancedreports')->__('Filter by Sale Items'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/*', $currentParams)."')",
        ));

        // Filter by non sale items bytton
        $currentParams = $this->getRequest()->getParams();
        if (array_key_exists('filter_sale',$currentParams))		unset($currentParams['filter_sale']);
        $currentParams['filter_nonsale'] = true;

        $this->_addButtonLabel = Mage::helper('advancedreports')->__('Filter by Non Sale Items');
        $this->_addButton('filter_nonsale', array(
            'label'     => Mage::helper('advancedreports')->__('Filter by Non Sale Items'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/*', $currentParams)."')",
        ));

        // Unfilter button
        $currentParams = $this->getRequest()->getParams();
        if (array_key_exists('filter_sale',$currentParams))	unset($currentParams['filter_sale']);
        if (array_key_exists('filter_nonsale',$currentParams))	unset($currentParams['filter_nonsale']);

        $this->_addButtonLabel = Mage::helper('advancedreports')->__('Unfilter');
        $this->_addButton('unfilter', array(
            'label'     => Mage::helper('advancedreports')->__('Unfilter'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/*', $currentParams)."')",
        ));
        /**** END CUSTOM FACTORY X CODE ****/
    }
}