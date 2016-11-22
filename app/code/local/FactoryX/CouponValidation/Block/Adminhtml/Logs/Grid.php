<?php

/**
 * Class FactoryX_CouponValidation_Block_Adminhtml_Logs_Grid
 */
class FactoryX_CouponValidation_Block_Adminhtml_Logs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('logsGrid');
        $this->setDefaultSort('log_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('couponvalidation/log_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('log_id', array(
            'header' => Mage::helper('couponvalidation')->__('Log #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'log_id',
        ));

        $this->addColumn('coupon_code', array(
            'header' => Mage::helper('couponvalidation')->__('Coupon Code'),
            'align' => 'left',
            'index' => 'coupon_code',
        ));

        $this->addColumn('rule_id', array(
            'header' => Mage::helper('couponvalidation')->__('Rule #'),
            'align' => 'left',
            'index' => 'rule_id',
        ));

        $this->addColumn('comment', array(
            'header' => Mage::helper('couponvalidation')->__('Comment'),
            'align' => 'left',
            'index' => 'comment',
        ));

        $this->addColumn('admin_user', array(
            'header' => Mage::helper('couponvalidation')->__('Admin User'),
            'align' => 'left',
            'index' => 'admin_user',
        ));

        $this->addColumn('ip_address', array(
            'header' => Mage::helper('couponvalidation')->__('IP Address'),
            'align' => 'left',
            'index' => 'ip_address',
        ));

        $this->addColumn('store_code', array(
            'header' => Mage::helper('couponvalidation')->__('Store Code'),
            'align' => 'left',
            'index' => 'store_code',
        ));

        $this->addColumn('added', array(
            'header' => Mage::helper('contests')->__('Created At'),
            'index' => 'added',
            'width' => '120px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('log_id');
        $this->getMassactionBlock()->setFormFieldName('logs');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('couponvalidation')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('couponvalidation')->__('Are you sure?')
        ));

        return $this;
    }

}
