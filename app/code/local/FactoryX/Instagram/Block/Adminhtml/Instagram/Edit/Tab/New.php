<?php
/**
 * This block is slightly different as it displays a grid of the approved images of the list
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Edit_Tab_New extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @return mixed
     */
    protected function _prepareLayout()
    {
        $this->setChild('update_images_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('instagram')->__('Update Images List'),
                    'onclick'   => "setLocation('{$this->getUrl('*/*/update', array('id'  =>  $this->getListId()))}back/edit/')",
                    'class'   => 'add'
                ))
        );

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getUpdateImagesButtonHtml()
    {
        return $this->getChildHtml('update_images_button');
    }

    /**
     * @return string
     */
    public function getButtonsHtml()
    {
        $html = '';
        $html.= $this->getUpdateImagesButtonHtml();
        return $html;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('instagram_new');
        $this->setUseAjax(true);
        $this->setTemplate('factoryx/instagram/new.phtml');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'instagram/instagramimage_collection';
    }

    /**
     * Retrieve collection class
     *
     * @return FactoryX_Instagram_Model_Resource_Instagramimage_Collection
     */
    protected function _prepareCollection()
    {
        // We filter the images by list id
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('*')
            ->addListFilter($this->getRequest()->getParam('id'))
            ->addFilter('is_approved', 0)
            ->addFilter('is_visible', 1);

        $collection->getSelect()->reset(Zend_Db_Select::LIMIT_COUNT);
        $collection->getSelect()->reset(Zend_Db_Select::LIMIT_OFFSET);
        $collection->clear();
        $collection->setPageSize(false);
        $collection->load();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Retrieve list id
     *
     * @return int
     */
    public function getListId()
    {
        return Mage::registry('instagramlist_data')->getListId();
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('instagram')->__('New Image(s)');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return Mage::helper('instagram')->__('New Image(s)');
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
}