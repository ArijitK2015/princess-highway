<?php
 
class Xigmapro_Jobs_Adminhtml_JobsController extends Mage_Adminhtml_Controller_Action
{
 
    protected function _initAction()
    {
        $this->loadLayout();
        
        //$this->_setActiveMenu('Jobs/items');
        $this->_setActiveMenu('factoryx_menu/factoryx_menu_manage_jobs');
        
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            
        return $this;
    }   
   
    public function indexAction() {
        $this->_initAction();       
        $this->_addContent($this->getLayout()->createBlock('Jobs/adminhtml_Jobs'));
        $this->renderLayout();
    }
 
    public function editAction()
    {
        $JobsId     = $this->getRequest()->getParam('id');
        $JobsModel  = Mage::getModel('Jobs/Jobs')->load($JobsId);
 
        if ($JobsModel->getId() || $JobsId == 0) {
 
            Mage::register('Jobs_data', $JobsModel);
 
            $this->loadLayout();
            //$this->_setActiveMenu('Jobs/items');
            $this->_setActiveMenu('factoryx_menu/factoryx_menu_manage_jobs');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('Jobs/adminhtml_Jobs_edit'))
                 ->_addLeft($this->getLayout()->createBlock('Jobs/adminhtml_Jobs_edit_tabs'));
               
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Jobs')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
   
    public function newAction()
    {
        $this->_forward('edit');
    }
   
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
				
                $JobsModel = Mage::getModel('Jobs/Jobs');
               
                $JobsModel->setId($this->getRequest()->getParam('id'))
                    ->setPosition($postData['position'])
					->setStatus($postData['status'])
					->setHours($postData['hours'])
					->setEntitlements($postData['entitlements'])
					->setEmail($postData['email'])
					->setRequirements($postData['requirements'])
					->setCountrys($postData['countrys'])
					->setLocations($postData['locations'])
					->setStatuss($postData['statuss'])
                    ->save();
               //print_r($JobsModel);
			   //exit;
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Job was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setJobsData(false);
 
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setJobsData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
   
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $JobsModel = Mage::getModel('Jobs/Jobs');
               
                $JobsModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Job was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('importedit/adminhtml_Jobs_grid')->toHtml()
        );
    }
}