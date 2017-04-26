<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Account_Edit_Tabs
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Account_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Constructor for the tabs of the edit homepage page
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('account_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('shippedfrom')->__('Account Information'));
    }

    /**
     * Prepare the HTML before displaying it
     */
    protected function _beforeToHtml()
    {
        // Add the general tab
        $this->addTab(
            'general_tab',
            array(
                'label'     => Mage::helper('shippedfrom')->__('Account Information'),
                'title'     => Mage::helper('shippedfrom')->__('Account Information'),
                'content'   => $this->getLayout()
                                    ->createBlock('shippedfrom/adminhtml_account_edit_tab_general')
                                    ->toHtml()
            )
        );

        // Add the media tab
        $this->addTab(
            'product_tab',
            array(
                'label' => Mage::helper('shippedfrom')->__('Products'),
                'title' => Mage::helper('shippedfrom')->__('Products'),
                'content' => $this->getLayout()
                                    ->createBlock('shippedfrom/adminhtml_account_edit_tab_product')
                                    ->toHtml()
            )
        );

        return parent::_beforeToHtml();
    }

}