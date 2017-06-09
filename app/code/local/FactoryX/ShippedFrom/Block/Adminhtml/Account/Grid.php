<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Account_Grid
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Account_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_Account_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('accountGrid');
        $this->setDefaultSort('account_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var FactoryX_ShippedFrom_Model_Resource_Account_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/account_collection')
            ->addStoreLocationsValues();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'account_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('ID'),
                'align' => 'left',
                'width' => '10px',
                'index' => 'account_id',
                'column_css_class' => 'no-display', // class of the column row item
                'header_css_class' => 'no-display', // class of the column header
                'is_system' => true
            )
        );

        $this->addColumn(
            'account_no',
            array(
                'header' => Mage::helper('shippedfrom')->__('Account Number'),
                'width' => '50px',
                'align' => 'left',
                'index' => 'account_no'
            )
        );

        $this->addColumn(
            'merchant_location_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('MLID'),
                'width' => '50px',
                'align' => 'left',
                'index' => 'merchant_location_id',
            )
        );

        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('shippedfrom')->__('Account Name'),
                'align' => 'left',
                'width' => '150px',
                'index' => 'name'
            )
        );

        $this->addColumn(
            'location_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('Assigned Store/State'),
                'align' => 'left',
                'index' => 'location_id',
                'renderer' => 'FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Location',
                'width' => '250px'
            )
        );

        $this->addColumn(
            'valid_from',
            array(
                'header' => Mage::helper('shippedfrom')->__('Valid From'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'valid_to',
                'width' => '80px'
            )
        );

        $this->addColumn(
            'valid_to',
            array(
                'header' => Mage::helper('shippedfrom')->__('Valid To'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'valid_to',
                'width' => '80px'
            )
        );


        $link = Mage::helper('adminhtml')->getUrl('adminhtml/shippedfromaccount/delete', array('id' => '$account_id'));

        $this->addColumn(
            'action_view',
            array(
                'header'   => $this->helper('shippedfrom')->__('Action'),
                'width'    => 15,
                'sortable' => false,
                'filter'   => false,
                'is_system' => true,
                'type'     => 'action',
                'actions'  => array(
                    array(
                        'url'     => $link,
                        'caption' => $this->helper('shippedfrom')->__('Delete'),
                    ),
                )
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('shippedfrom')->__('CSV'));

        return parent::_prepareColumns();
    }

    /**
     * Getter for the row URL
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}