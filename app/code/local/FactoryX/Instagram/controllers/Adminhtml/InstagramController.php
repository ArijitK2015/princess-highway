<?php
class FactoryX_Instagram_Adminhtml_InstagramController extends Mage_Adminhtml_Controller_Action
{
    const UPDATE_TYPE_USER = 1;
    const UPDATE_TYPE_TAG = 0;

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/instagram');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('factoryx_menu/instagram');

        return $this;
    }

    public function logAction()
    {
        try {
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('instagram/adminhtml_instagram_log'));
            $this->renderLayout();
        }
        catch(Exception $ex) {
            Mage::helper('instagram')->log(sprintf("%s->error=%s", __METHOD__, print_r($ex, true)), Zend_Log::DEBUG );
        }
    }

    public function indexAction()
    {
        try {
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('instagram/adminhtml_instagram'));
            $this->renderLayout();
        }
        catch(Exception $ex) {
            Mage::helper('instagram')->log(sprintf("%s->error=%s", __METHOD__, print_r($ex, true)), Zend_Log::DEBUG );
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('instagram/instagramlist')->load($id);
        
        if ($id != 0) {
            if ($model->getId() == 0) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('instagram')->__('List does not exist!'));
            }
            else {
                Mage::helper('instagram')->log(sprintf("%s->id[%d]=%s", __METHOD__, $id, get_class($model)), Zend_Log::DEBUG );
            }
            //$this->_redirect('*/*/');
        }

        // Data from the form
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

        // If data from the form, we add it to the model
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('instagramlist_data', $model);        

        $this->loadLayout();
        $this->_setActiveMenu('factoryx_menu/instagram');

        // Mage_Core_Model_Layout
        $layout = $this->getLayout();
        $blockHead = $layout->getBlock('head');
		$blockHead->setCanLoadExtJs(true);
        $blockEdit = $layout->createBlock('instagram/adminhtml_instagram_edit');
        $blockEditTabs = $layout->createBlock('instagram/adminhtml_instagram_edit_tabs');
        $this->_addContent($blockEdit)->_addLeft($blockEditTabs);
        $blockHead->setCanLoadTinyMce(true);
        $this->renderLayout();
    }

    public function updateAction()
    {
        $id = $this->getRequest()->getParam('id');
        Mage::helper('instagram')->log(sprintf("%s->id: %s", __METHOD__, $id));
        if (!$id) {
            Mage::helper('instagram')->log(sprintf("no id"));
            return;
        }
        $list = Mage::getModel('instagram/instagramlist')->load($id);
        $updateType = $list->getUpdatetype();
        Mage::helper('instagram')->log(sprintf("%s->updateType: %s|%s", __METHOD__, $updateType, get_class($list)));
        switch ($updateType) {
            case FactoryX_Instagram_Model_Instagramlist::UPDATE_TYPE_TAG :
                $result = Mage::helper('instagram/image')->update($list->getTags(), $id);
                Mage::helper('instagram')->log(sprintf("%s->result: %s", __METHOD__, $result));
                if (!$result) {
                    $message = $this->__('An error occured. Make sure you are authenticated with Instagram.');
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
                else {
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('instagram')->__('List was successfully updated'));
                }
                break;

            case FactoryX_Instagram_Model_Instagramlist::UPDATE_TYPE_USER :
                if (!Mage::getModel('instagram/instagramauth')->isValid()) {
                    $message = $this->__('Need Instagram user authentification');
                    Mage::getSingleton('adminhtml/session')->addError($message);
                    break;
                }

                $result  = Mage::helper('instagram/image_user')->update($list->getUsers(), $id);
                $message = $this->__('An error occured. Make sure you are authenticated with Instagram.');
                if (!$result) {
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
                else
                {
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('instagram')->__('List was successfully updated'));
                }
                break;
        }

        Mage::getSingleton('admin/session')->setActiveTab('new_tab');

        if ($this->getRequest()->getParam('back')) {
            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }
        $this->_redirect('instagram/adminhtml_instagram/index');
        return;
    }

    public function orderAction()
    {
        $pos_array  = explode(",", $this->getRequest()->getPost('image'));
        $link_array = (array)(json_decode($this->getRequest()->getPost('links')));
        $caption_array = (array)(json_decode($this->getRequest()->getPost('captions')));
        $max_count  = count($pos_array) + 1;

        $images = Mage::getModel('instagram/instagramimage')->getCollection()->addFieldToSelect(array('image_id', 'image_order'))->addFieldToFilter('is_approved', 1);
        Mage::getSingleton('core/resource_iterator')->walk($images->getSelect(), array(array($this, 'orderActionCallback')), array('captions' => $caption_array,'links' => $link_array, 'positions' => array_flip($pos_array)));
        Mage::getSingleton('adminhtml/session')->addSuccess('Success');
        $this->_redirectReferer();
    }

    /**
     * @param $args
     */
    public function orderActionCallback($args)
    {
        $image = Mage::getModel('instagram/instagramimage');
        $image->setData($args['row']);
        $image_id  = $image->getData('image_id');
        $positions = $args['positions'];
        $links     = $args['links'];
        $captions  = $args['captions'];

        // set position
        if (isset($positions[$image_id])) {
            $image->setData('image_order', ($positions[$image_id] + 1));
        }else{
            $image->setData('image_order', (sizeof($positions) + 1));
        }

        // set link
        if (!empty($links[$image_id])) {
            $image->setData('link', $links[$image_id]);
        }else{
            $image->setData('link', "");
        }

        // set caption
        if (isset($captions[$image_id])) {
            $image->setData('caption_text', $captions[$image_id]);
        }

        $image->getResource()->saveAttribute($image, array('image_order','link','caption_text'));
    }

    public function loadtaggedproductAction(){
        $url = $this->getRequest()->getParam('url');
        $tag = $this->getRequest()->getParam('hash_tag');
        if (!$url){
            $url = Mage::helper('instagram/image')->getEndpointUrl($tag)."&count=40";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if (Mage::getStoreConfig('factoryx_instagram/module_options/proxy')){
            Mage::helper('instagram')->log(sprintf("%s->proxy=%s", __METHOD__, Mage::getStoreConfig('factoryx_instagram/module_options/proxy')), Zend_Log::DEBUG );
            curl_setopt($ch, CURLOPT_PROXY, Mage::getStoreConfig('factoryx_instagram/module_options/proxy'));
        }
        $output = curl_exec($ch);
        curl_close($ch);
        $this->getResponse()->setBody($output);
    }

    public function approveAction()
    {
        $imageId = $this->getRequest()->getPost('id');

        $image = Mage::getModel('instagram/instagramimage')->load($imageId);

        if ($image->getId()) {
            $image->setIsApproved(1);
            $image->getResource()->saveAttribute($image,array('is_approved'));
        }

        $this->getResponse()->setBody(json_encode(array('success' => true)));
    }

    public function deleteAction() {
        $listId = (int) $this->getRequest()->getParam('id');
        if ($listId) {
            try {
                $model = Mage::getModel('instagram/instagramlist')->load($listId);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('instagram')->__('List was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteimageAction()
    {
        $imageId = $this->getRequest()->getPost('id');

        $image = Mage::getModel('instagram/instagramimage')->load($imageId);

        if ($image->getId()) {
            $image->setIsVisible(0);
            $image->getResource()->saveAttribute($image,array('is_visible'));
        }

        $this->getResponse()->setBody(json_encode(array('success' => true)));
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('instagram/instagramlist');

            try
            {

                // Assign the data to the model
                $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

                // Save the list
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('instagram')->__('List was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                if ($model->getId())
                {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else
                {
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('instagram')->__('Unable to find list to save'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $instagramIds = $this->getRequest()->getParam('instagram');
        if (!is_array($instagramIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select list(s)'));
        } else {
            try {
                foreach ($instagramIds as $instagramId) {
                    $model = Mage::getModel('instagram/instagramlist')->load($instagramId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($instagramIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function masslogDeleteAction()
    {
        $logIds = $this->getRequest()->getParam('instagram');
        if (!is_array($logIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select log(s)'));
        } else {
            try {
                foreach ($logIds as $logId) {
                    $model = Mage::getModel('instagram/instagramlog')->load($logId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($logIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/log');
    }

    public function navigationAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('instagram/instagramlist')->load($id);

        Mage::register('instagramlist_data', $model);

        $this->loadLayout();
        $this->renderLayout();
    }
}