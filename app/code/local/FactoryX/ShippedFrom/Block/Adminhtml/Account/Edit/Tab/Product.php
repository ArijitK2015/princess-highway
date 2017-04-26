<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Account_Edit_Tab_Product
 * This is the edit form
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Account_Edit_Tab_Product
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('account_product');
        $this->setUseAjax(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'shippedfrom/account_product_collection';
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect("*")
            ->addFieldToFilter('associated_account', $this->getRequest()->getParam('id'));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'type',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Type'),
                'index'     => 'type',
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
            'product_id',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Product Id'),
                'index'     => 'product_id'
            )
        );

        $this->addColumn(
            'contract_valid_from',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Valid From'),
                'index'     => 'contract_valid_from',
                'type'      => 'datetime' 
            )
        );

        $this->addColumn(
            'contract_valid_to',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Valid To'),
                'index'     => 'contract_valid_to',
                'type'      => 'datetime'
            )
        );

        $this->addColumn(
            'contract_expired',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Expired'),
                'index'     => 'contract_expired',
                'type'      => 'options',
                'options'   =>  array(
                    1   =>  Mage::helper('shippedfrom')->__('Yes'),
                    0   =>  Mage::helper('shippedfrom')->__('No'),
                )
            )
        );

        $this->addColumn(
            'contract_volumetric_pricing',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Volumetric Pricing'),
                'index'     => 'contract_volumetric_pricing',
                'type'      => 'options',
                    'options'   =>  array(
                        1   =>  Mage::helper('shippedfrom')->__('Yes'),
                        0   =>  Mage::helper('shippedfrom')->__('No'),
                    )
            )
        );

        $this->addColumn(
            'contract_cubing_factor',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Cubing Factor'),
                'index'     => 'contract_cubing_factor'
            )
        );

        $this->addColumn(
            'contract_max_item_count',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Max Item Count'),
                'index'     => 'contract_max_item_count'
            )
        );

        $this->addColumn(
            'authority_to_leave_threshold',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Authority To Leave Threshold'),
                'index'     => 'authority_to_leave_threshold'
            )
        );
        /*
        $this->addColumn(
            'credit_blocked',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Credit Blocked'),
                'index'     => 'credit_blocked'
            )
        );
        */

        $this->addColumn(
            'associated_shipping_method',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Associated Shipping Method'),
                'index'     => 'associated_shipping_method'
            )
        );

        $link = Mage::helper('adminhtml')->getUrl(
            'adminhtml/shippedfromaccount/deleteProduct',
            array('id' => '$entity_id')
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
        $this->addColumn(
            'action_view',
            array(
                'header'   => $this->helper('shippedfrom')->__('Action'),
                'width'    => 15,
                'sortable' => false,
                'filter'   => false,
                'type'     => 'action',
                'actions'  => array(
                    array(
                        'url'     => $link,
                        'caption' => $this->helper('shippedfrom')->__('Delete'),
                    ),
                )
            )
        );
        */
        
        return parent::_prepareColumns();
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('shippedfrom')->__('Products');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return Mage::helper('shippedfrom')->__('Products');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Getter for the row URL
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }


}