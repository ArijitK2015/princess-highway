<?php

/**
 * Class FactoryX_Merchandising_Adminhtml_IndexController
 */
class FactoryX_Merchandising_Adminhtml_MerchandisingController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/merchandising');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction() {
        //Mage::helper('merchandising')->log(sprintf("%s->requestParams=%s", __METHOD__, print_r($this->getRequest()->getParams(), true)) );
        
        $cat_id = $this->getRequest()->getParam('cat_id');
        $store_id = $this->getRequest()->getParam('store_id');

        //Mage::helper('merchandising')->log(sprintf("%s->store|cat:%s|%d", __METHOD__, $store_id, $cat_id) );

        // Visibilities array
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
        );

        // Add store filter
        if (!$store_id) {
            $store_id = 0;
        }
        //Mage::helper('merchandising')->log(sprintf("%s->setCurrentStore: %d", __METHOD__, $store_id) );
        Mage::app()->setCurrentStore($store_id);

        // Load category
        Mage::helper('merchandising')->log(sprintf("%s->catalog/category: %d", __METHOD__, $cat_id) );
        $category = Mage::getModel('catalog/category')->load($cat_id);
        // Get products from category sorted by position
        $collection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($category);
        // Sort by position
        $collection->addAttributeToSort('position','asc');
        // Add attributes to the collection
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToSelect('special_price');
        // Filter with all visibilities
        $collection->addAttributeToFilter('visibility', $visibility);
        // Add small_image to the collection
        $collection->joinAttribute('small_image', 'catalog_product/image', 'entity_id', null, 'left');
        // Add stock status
        $collection->joinTable(
            'cataloginventory/stock_status',
            'product_id = entity_id',
            array(
                "stock_status" => "stock_status"
            ),
            null,
            'left'
        );

        //Mage::helper('merchandising')->log(sprintf("%s->SQL: %s", __METHOD__, $collection->getSelect()->__toString()) );

        // fix Item (Mage_Catalog_Model_Product) with the same id "X" already exist (avoid using distinct)
        $collection->getSelect()->group('entity_id');
        
        $this->loadLayout();

        $view = $this->getLayout()->getBlock('merchandising');
        //Mage::helper('merchandising')->log(sprintf("%s->merchandising=%s", __METHOD__, get_class($view)) );
        
        $view->setData('category',$category);
        $view->setData('products',$collection);
        $view->setData('width',Mage::getStoreConfig('merchandising/imagesize/width'));
        $view->setData('height',Mage::getStoreConfig('merchandising/imagesize/height'));
        
        $this->renderLayout();
    }

    public function saveAction()
    {
        $pos_array = explode(",",$this->getRequest()->getPost('prod'));
        $cat_id = $this->getRequest()->getPost('cat_id');
        $invalid_ids = unserialize(stripslashes($this->getRequest()->getPost('invalid_ids')));
        $max_count = count($pos_array)+1;
        
        // validate input
        if (!$cat_id) {
            Mage::getSingleton('adminhtml/session')->addError('No category id provided!');
            $this->_redirect('*/*/index');
        }
        
        $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = sprintf("UPDATE catalog_category_product SET position = %d WHERE category_id = %d", $max_count, $cat_id);
        $writeConnection->query($query);
        
        foreach ($pos_array as $position => $product_id) {
            $truePosition = $position+1;
            $query = "UPDATE catalog_category_product SET position = $truePosition WHERE category_id = $cat_id AND product_id = $product_id";
            $writeConnection->query($query);
        }
        if ((is_array($invalid_ids)) && (count($invalid_ids) > 0)) {
            foreach($invalid_ids as $invalid_id){
                $query = "DELETE FROM catalog_category_product WHERE category_id = $cat_id AND product_id = $invalid_id";
                $writeConnection->query($query);
            }
        }
        // Reindex
        $process = Mage::getSingleton('index/indexer')->getProcessByCode("catalog_category_product");
        $process->reindexEverything();
        Mage::getSingleton('adminhtml/session')->addSuccess('Product Merchandising Saved');
        $this->_redirect('*/*/view', array('cat_id' => $cat_id));
    }


}