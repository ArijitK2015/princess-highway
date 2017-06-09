<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Log_Grid
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_Log_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('logGrid');
        $this->setDefaultSort('log_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var FactoryX_ShippedFrom_Model_Resource_Cron_Log_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/cron_log_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'log_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('ID'),
                'align' => 'left',
                'index' => 'log_id',
                'column_css_class' => 'no-display', // class of the column row item
                'header_css_class' => 'no-display', // class of the column header
                'is_system' => true
            )
        );

        $this->addColumn(
            'cron_name',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Cron Job Code'),
                'index'     => 'cron_name',
            )
        );

        $this->addColumn(
            'summary',
            array(
                'header'    => Mage::helper('shippedfrom')->__('Summary'),
                'index'     => 'summary',
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('shippedfrom')->__('Created At'),
                'align' =>  'left',
                'index' => 'created_at',
                'type' => 'datetime',
                'gmtoffset' => true,
                'default' => ' -- '
            )
        );

        return parent::_prepareColumns();
    }

}