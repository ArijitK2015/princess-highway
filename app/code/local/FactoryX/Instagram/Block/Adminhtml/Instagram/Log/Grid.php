<?php

/**
 * Class FactoryX_Instagram_Block_Adminhtml_Instagram_Log_Grid
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *	Constructor the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('instagramlogGrid');
        $this->setDefaultSort('log_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     *	Prepare the collection to display in the grid
     */
    protected function _prepareCollection()
    {
        // Get the collection of instagram lists
        $collection = Mage::getModel('instagram/instagramlog')->getCollection()
            ->addFieldToSelect(array('log_id','list_id','image_id','added'));

        // Add image to the collection and list title
        $collection->getSelect()
            ->joinInner(
                array('instagram_image' =>	Mage::getSingleton("core/resource")->getTableName('fx_instagram_image')),
                "instagram_image.image_id = main_table.image_id",
                array ("image" => "instagram_image.standard_resolution_url"))
            ->joinInner(
                array('instagram_list' =>	Mage::getSingleton("core/resource")->getTableName('fx_instagram_list')),
                "instagram_list.list_id = main_table.list_id",
                array ("title" => "instagram_list.title"));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     *	Prepare the columns of the grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('log_id', array(
            'header' => Mage::helper('instagram')->__('Image Log #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'log_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('instagram')->__('List Title'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'title',
        ));

        $this->addColumn('image', array(
            'header' => Mage::helper('instagram')->__('Image'),
            'width' => '150px',
            'renderer'  => 'instagram/adminhtml_instagram_log_grid_renderer_image',
            'index' => 'image',
        ));

        $this->addColumn('added', array(
            'header' => Mage::helper('homepage')->__('First imported At'),
            'index' => 'added',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        return parent::_prepareColumns();
    }

    /**
     *	Prepare mass actions
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('log_id');
        $this->getMassactionBlock()->setFormFieldName('instagram');

        // Delete action
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('instagram')->__('Delete'),
            'url' => $this->getUrl('*/*/masslogDelete'),
            'confirm' => Mage::helper('instagram')->__('Are you sure?')
        ));

        return $this;
    }

}
