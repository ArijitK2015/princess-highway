<?php

/**
 * Class FactoryX_Instagram_Block_Adminhtml_Instagram_Grid
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *	Constructor the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('instagramGrid');
        $this->setDefaultSort('instagram_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     *	Prepare the collection to display in the grid
     */
    protected function _prepareCollection()
    {
        // Get the collection of instagram lists
        $collection = Mage::getModel('instagram/instagramlist')->getCollection()
            ->addFieldToSelect(array('list_id','title','link','updatetype','tags','users','image_size','style','hover','show_per_page','limit'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     *	Prepare the columns of the grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('list_id', array(
            'header' => Mage::helper('instagram')->__('Image List #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'list_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('instagram')->__('List Title'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'title',
        ));

        $this->addColumn('link', array(
            'header' => Mage::helper('instagram')->__('Link'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'link',
        ));

        $types = Mage::getSingleton('instagram/instagramlist')->getTypesOptionArray();

        $this->addColumn('updatetype', array(
            'header' => Mage::helper('instagram')->__('Update Type'),
            'align' => 'left',
            'width'	=>	'50px',
            'index' => 'updatetype',
            'type'  => 'options',
            'options'   => $types
        ));

        $this->addColumn('tags', array(
            'header' => Mage::helper('instagram')->__('Tags'),
            'align' => 'left',
            'index' => 'tags',
        ));

        $this->addColumn('users', array(
            'header' => Mage::helper('instagram')->__('Users'),
            'align' => 'left',
            'index' => 'users',
        ));

        $this->addColumn('image_size', array(
            'header' => Mage::helper('instagram')->__('Images Size'),
            'align' => 'left',
            'index' => 'image_size',
        ));

        $this->addColumn('style', array(
            'header' => Mage::helper('instagram')->__('Style'),
            'align' => 'left',
            'index' => 'style',
        ));

        $this->addColumn('hover', array(
            'header' => Mage::helper('instagram')->__('Hover'),
            'index' => 'hover'
        ));

        $this->addColumn('show_per_page', array(
            'header' => Mage::helper('instagram')->__('Show Per Page'),
            'index' => 'show_per_page'
        ));

        $this->addColumn('limit', array(
            'header' => Mage::helper('instagram')->__('Limit'),
            'index' => 'limit'
        ));

        $this->addColumn('action', array(
            'header'            => Mage::helper('instagram')->__('Action'),
            'width'             => '100',
            'type'              => 'action',
            'getter'            => 'getId',
            'frame_callback'    => array($this, 'decorateRow'),
            'actions'           => array(
                array(
                    'caption'   => 'Edit',
                    'url'       => array('base'    =>  '*/*/edit'),
                    'field'     => 'id'
                )
            ),
            'sortable'          => false,
            'filter'            => false,
            'index'             =>  'stores'
        ));

        return parent::_prepareColumns();
    }

    /**
     * append a view action
     *
     * @param $sVal
     * @param Mage_Core_Model_Abstract $oRow
     * @return string
     */
    public function decorateRow($sVal, Mage_Core_Model_Abstract $oRow) {
        // get the frontend url
        $storeId = Mage::app()->getWebsite($id = true)->getDefaultGroup()->getDefaultStoreId();
        $frontUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $sVal .= sprintf(" | <a href='%sinstagrams/gallery/list/id/%d' target='_blank' rel='noopener noreferrer'>View</a>", $frontUrl, $oRow->getId());
        return $sVal;
    }

    /**
     *	Prepare mass actions
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('list_id');
        $this->getMassactionBlock()->setFormFieldName('instagram');

        // Delete action
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('instagram')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('instagram')->__('Are you sure?')
        ));

        return $this;
    }

    /**
     *    Getter for the row URL
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
