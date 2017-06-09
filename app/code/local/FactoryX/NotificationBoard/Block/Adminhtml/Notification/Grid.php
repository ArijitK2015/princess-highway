<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why:  
 */
class FactoryX_NotificationBoard_Block_Adminhtml_Notification_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        $this->setDefaultSort('notification_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return mixed
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('factoryx_notificationboard/notification')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn('notification_id', array(
            'header' => $this->__('ID'),
            'index'  => 'notification_id',
            'width'  => '10px'
        ));

        $this->addColumn('notification_title', array(
            'header' => $this->__('Notification Title'),
            'index'  => 'notification_title',
            'type'   => "text"
        ));

        $this->addColumn('status', array(
            'header' => $this->__('Status'),
            'index'  => 'status',
            'type'   => 'options',
            'width'  => '100px',
            'options' => array(
                0 => 'Disable',
                1 => 'Enable'
            )
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $this->addColumn('start_date', array(
            'header' => $this->__('Start Date'),
            'index'  => 'start_date',
            'width'  => '250px',
            'format' => $dateFormatIso,
            'type'   => "date"
        ));

        $this->addColumn('end_date', array(
            'header' => $this->__('End Date'),
            'index'  => 'end_date',
            'width'  => '250px',
            'format' => $dateFormatIso,
            'type'   => "date"
        ));

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));

        $this->addExportType('*/*/exportExcel', $this->__('Excel XML'));
        
        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function getRowUrl($row)
    {
       return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $modelPk = Mage::getModel('factoryx_notificationboard/notification')->getResource()->getIdFieldName();
        $this->setMassactionIdField($modelPk);
        $this->getMassactionBlock()->setFormFieldName('ids');
        // $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> $this->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
        ));
        return $this;
    }
    }
