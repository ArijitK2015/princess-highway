<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Product_Grid
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_Product_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var FactoryX_ShippedFrom_Model_Resource_Account_Product_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/account_product_collection')
            ->addAccountsData();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('ID'),
                'align' => 'left',
                'index' => 'entity_id',
                'column_css_class' => 'no-display', // class of the column row item
                'header_css_class' => 'no-display', // class of the column header
                'is_system' => true
            )
        );

        $this->addColumn(
            'associated_account',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Account Name'),
                'index'     => 'associated_account',
                'renderer' => 'shippedfrom/adminhtml_product_grid_renderer_account'
            )
        );

        $this->addColumn(
            'group',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Group'),
                'index'     => 'group',
            )
        );

        $this->addColumn(
            'type',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Type'),
                'index'     => 'type',
            )
        );

        $this->addColumn(
            'product_id',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Product Id'),
                'index'     => 'product_id'
            )
        );

        $this->addColumn(
            'associated_shipping_method',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Associated Shipping Method'),
                'index'     => 'associated_shipping_method'
            )
        );
        
        $this->addColumn(
            'action',
            array(
                'header' => Mage::helper('shippedfrom')->__('Action'),
                'width' => '100px',
                'sortable' => false,
                'filter' => false,
                'is_system' => true,
                'renderer' => 'shippedfrom/adminhtml_product_grid_renderer_action'
            )
        );

        /*
        // grop down actions
        $actionView = Mage::helper('adminhtml')->getUrl('adminhtml/shippedfromproduct/view', array('id' => '$entity_id'));
        $actionDelete = Mage::helper('adminhtml')->getUrl('adminhtml/shippedfromproduct/delete', array('id' => '$entity_id'));
        $this->addColumn(
            'action_view',
            array(
                'header'   => $this->helper('shippedfrom')->__('Action'),
                'width'    => 15,
                'sortable' => false,
                'filter'   => false,
                'type'     => 'action',
                'is_system' => true,
                'actions'  => array(
                    array(
                        'url'     => $actionView,
                        'caption' => $this->helper('shippedfrom')->__('View'),
                    ),
                    array(
                        'url'     => $actionDelete,
                        'caption' => $this->helper('shippedfrom')->__('Delete'),
                    )                    
                )
            )
        );
        */
        
        $this->addExportType('*/*/exportCsv', Mage::helper('shippedfrom')->__('CSV'));

        return parent::_prepareColumns();
    }

}