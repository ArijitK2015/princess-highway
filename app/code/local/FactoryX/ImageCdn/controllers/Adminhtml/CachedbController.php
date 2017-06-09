<?php

/**
 * Class FactoryX_ImageCdn_Adminhtml_CachedbController
 */
class FactoryX_ImageCdn_Adminhtml_CachedbController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/tools/imagecdn');
    }

    /**
     * @return $this
     */
    public function indexAction(){

        if($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setBody($this->getLayout()->createBlock('factoryx_imagecdn/adminhtml_cachedb_grid')->toHtml());
            return $this;
        }

        $this->_title($this->__('ImageCDN'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('factoryx_imagecdn/adminhtml_cachedb'));
        $this->renderLayout();
    }

    public function reuploadAction(){
        $cachedb = Mage::getModel('imagecdn/cachedb')->load($this->getRequest()->getParam('id'));
        $cachedb->reupload();
        return;
    }

    public function massReuploadAction(){
        $ids = $this->getRequest()->getParam('reupload');
        $total = count($ids);
        $counter = 0;
        foreach ($ids as $id){
            $cachedb = Mage::getModel('imagecdn/cachedb')->load($id);
            if ($cachedb){
                if ($cachedb->reupload()){
                    $counter++;
                }
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess("$counter out of $total are successfully re-uploaded");
        $this->_redirect('*/*/index');
    }

    /**
     *	This is an internal redirect from new action to edit action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/tools/imagecdn');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('factoryx_imagecdn/adminhtml_cachedb_edit'));

        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();
    }

    public function saveAction()
    {
        // We retrieve the POST data
        if ($data = $this->getRequest()->getPost())
        {
            try
            {
                if (
                    array_key_exists('size',$data)
                    &&
                    array_key_exists('folder',$data)
                ) {
                    $apiKey = Mage::getStoreConfig('imagecdn/amazons3/access_key_id');
                    $apiSecret = Mage::getStoreConfig('imagecdn/amazons3/secret_access_key');
                    $bucket = Mage::getStoreConfig('imagecdn/amazons3/bucket');

                    $size = explode('x',$data['size']);
                    $dir = Mage::getBaseDir('media') . "/catalog/product/" . $data['folder'];
                    if (array_key_exists('limit',$data)) {
                        $limit = $data['limit'];
                    } else {
                        $limit = false;
                    }
                    $i = 0;
                    // Check if dir
                    if (is_dir($dir)){
                        // Open dir
                        if ($dh = opendir($dir)) {
                            // List the files
                            while (($file = readdir($dh)) !== false) {
                                if ($i == $limit) {
                                    break;
                                }
                                $filePath = $dir . "/" . $file;
                                if (is_file($filePath)) {

                                    foreach (Mage::app()->getWebsites() as $website) {
                                        // Get the website id
                                        $websiteId = $website->getWebsiteId();
                                        foreach ($website->getGroups() as $group) {
                                            $stores = $group->getStores();
                                            foreach ($stores as $store) {
                                                if (!$store->isAdmin()) {
                                                    Mage::app()->setCurrentStore($store->getStoreId());
                                                    $cachedImg = Mage::getModel('catalog/product_image')
                                                        ->setDestinationSubdir($data['type'])
                                                        ->setWidth($size[0])
                                                        ->setHeight($size[1])
                                                        ->setBaseFile($data['folder'] . "/" . $file)
                                                        ->resize()
                                                        ->saveFile();
                                                    Mage::log($cachedImg->getNewFile());
                                                    // Does this file already exists on S3?
                                                    if (!Mage::helper('imagecdn')->isImageExistOnS3($cachedImg->getNewFile())) {
                                                        Mage::log('image not on S3');
                                                        $client = new FactoryX_ImageCdn_Model_Adapter_Amazons3_Wrapper(array('key' => $apiKey, 'private_key' => $apiSecret));
                                                        $result = $client->uploadFile($bucket, substr($cachedImg->getNewFile(), 1), $cachedImg->getNewFile(), true);
                                                        Mage::log($result);
                                                    } else {
                                                        Mage::log('image already on S3');
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $i++;
                            }
                            closedir($dh);
                        }
                    }
                    Mage::app()->setCurrentStore(0);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('imagecdn')->__('Cold images were successfully warmed'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/new');
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('imagecdn')->__('Unable to warm cold images'));
        $this->_redirect('*/*/');
    }
}