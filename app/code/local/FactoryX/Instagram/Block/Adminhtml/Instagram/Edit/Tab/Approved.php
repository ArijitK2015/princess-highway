<?php
/**
 * This block is slightly different as it displays a grid of the approved images of the list
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Edit_Tab_Approved extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('instagram_approved');
        $this->setUseAjax(true);
        $this->setTemplate('factoryx/instagram/approved.phtml');
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
            ->addFilter('is_approved', 1)
            ->addFilter('is_visible', 1)
            ->setOrder('image_order','ASC');

        if ($curPage = $this->getRequest()->getParam('page')) {
            $collection->setCurPage($curPage);
        }

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
     * Retrieve image size
     *
     * @return int
     */
    public function getImageSize()
    {
        return Mage::registry('instagramlist_data')->getImageSize();
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('instagram')->__('Approved Image(s)');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return Mage::helper('instagram')->__('Approved Image(s)');
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