<?php
 
class FactoryX_Careers_Adminhtml_CareersController extends Mage_Adminhtml_Controller_Action {
 
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('factoryx_menu/manage_careers');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }   
   
    public function indexAction()
    {
        $this->_initAction();       
        $this->_addContent($this->getLayout()->createBlock('careers/adminhtml_careers'));
        $this->renderLayout();
    }
 
    public function editAction()
    {
        $careersId     = $this->getRequest()->getParam('id');
        $careersModel  = Mage::getModel('careers/careers')->load($careersId);
 
        if ($careersModel->getId() || $careersId == 0)
        {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('careers_data', $careersModel);
 
            $this->loadLayout();
            $this->_setActiveMenu('factoryx_menu/manage_careers');
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('careers/adminhtml_careers_edit'));
               
            $this->renderLayout();
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('careers')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
   
    public function newAction()
    {
        $this->_forward('edit');
    }
   
    public function saveAction()
    {
        if ($postData =  $this->getRequest()->getPost() )
        {

            $careersModel = Mage::getModel('careers/careers');

            try {
                $careersModel->setData($postData)->setId($this->getRequest()->getParam('id'));
                $careersModel->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('careers')->__('Career was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setCareersData(false);
 
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCareersData($postData);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
   
    public function deleteAction()
    {
        $careerId = (int) $this->getRequest()->getParam('id');

        if($careerId) {
            try {
                $model = Mage::getModel('careers/careers')->load($careerId);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('careers')->__('Career was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/manage_careers');
    }

    public function editCareerPageAction()
    {
        $blocks = array('careers-img','careers-list','careers-view');
        $blockNames = array('CMS Career Image','CMS List Image','CMS Detail Image');
        $btn = (int) $this->getRequest()->getParam('btn');
        if(empty($btn))$btn = 0;
        $block = Mage::getModel('cms/block');
        $block->load($blocks[$btn]);
        $blockId = $block->getId();
        if(!$blockId){
            $block->setTitle($blockNames[$btn]);
            $block->setIdentifier($blocks[$btn]);
            $block->setStores(array(0));
            $block->setIsActive(1);
            $block->setContent('<style></style>');
            $block->save();
            $blockId= $block->getId();
        }

        $this->_redirect("adminhtml/cms_block/edit/", array("block_id"=>$blockId));
    }
}