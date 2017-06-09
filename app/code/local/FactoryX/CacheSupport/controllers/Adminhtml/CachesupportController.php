<?php

class FactoryX_CacheSupport_Adminhtml_CachesupportController extends Mage_Adminhtml_Controller_Action{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/tools/cachesupport');
    }

    public function warmAction(){

        $helper = Mage::helper('factoryx_cachesupport');

        $this->loadLayout();

        $view = $this->getLayout()->getBlock('cachesupport');
        $view->setData('base_url',Mage::app()->getStore('default')->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));

        if ($helper->enableWarmCMS()){
            $view->setData('cms_pages',Mage::getModel('cms/page')
                ->getCollection()
                ->addFieldToSelect('identifier')
                ->addFieldToFilter('is_active','1')
                ->toOptionArray());
        }

        if ($helper->enableWarmCategory()){
            $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addFieldToFilter('is_active', array('eq'=>'1'))
                ->addFieldToFilter('include_in_menu', array('eq' => 1))
                ->addUrlRewriteToResult();

            $category_pages = array();
            $product_pages = array();

            foreach ($categories as $category){
                    $catStores = $category->getStoreIds();
                    foreach($catStores as $storeId) {
                        if ($storeId){
                            $appEmulation = Mage::getSingleton('core/app_emulation');
                            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
                            $category_pages[] = str_replace('index.php/','',$category->getUrl($category));
                            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

                        }
                    }

                    if ($helper->enableWarmProduct()){
                        $productCollection = Mage::getModel('catalog/product')->getCollection()
                            ->addAttributeToSelect('*')
                            ->addCategoryFilter($category)
                            ->addAttributeToFilter('status', array('eq' => 1))
                            ->addAttributeToFilter('visibility', array('eq' => 4))
                            ->addUrlRewrite()
                            ->load();

                        $_category = Mage::getModel('catalog/category')->load($category->getId());
                        foreach ($productCollection as $product){
                            $stores = $product->getStoreIds();
                            if ((!Mage::helper('factoryx_cachesupport')->getProductTop()) || ($product->getData('cat_index_position') <= Mage::helper('factoryx_cachesupport')->getProductTop())){
                                foreach($stores as $storeId){
                                    $product_pages[] = $product->setStoreId($storeId)->getUrlPath($_category);
                                }
                            }
                        }
                    }
            }

            $view->setData('category_pages',$category_pages);
            if ($helper->enableWarmProduct()){
                $view->setData('product_pages',$product_pages);
            }
        }
        $this->renderLayout();
    }

    public function crawlAction(){
        $helper = Mage::helper('factoryx_cachesupport');
        $urlToCrawl = $this->getRequest()->getParam('url');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlToCrawl);
        if ($helper->isDebugMode()){
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
        }else{
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
        }
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (Mage::helper('factoryx_cachesupport')->getProxy()){
            curl_setopt($ch, CURLOPT_PROXY, Mage::helper('factoryx_cachesupport')->getProxy());
        }
        $result = curl_exec($ch);
        if ($helper->isDebugMode()) {
            Mage::log($urlToCrawl);
            Mage::log($result);
        }
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = array();
        $response['http_status'] = $http_status;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

}
