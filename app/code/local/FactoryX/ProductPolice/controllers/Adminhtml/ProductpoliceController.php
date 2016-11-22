<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/10/2014
 * Why:  
 */

class FactoryX_ProductPolice_Adminhtml_ProductpoliceController extends Mage_Adminhtml_Controller_Action{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/product_police');
    }

    /**
     * @return $this
     */
    public function indexAction(){
        if($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setBody($this->getLayout()->createBlock('factoryx_productpolice_adminhtml/item_grid')->toHtml());
            return $this;
        }

        $this->_title($this->__('Faulty Product'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('factoryx_productpolice_adminhtml/item'));
        $this->renderLayout();
    }

    public function dbscanAction(){
        try{
            $observer = new FactoryX_ProductPolice_Model_Observer();
            $observer->recheck(null);
            Mage::getSingleton('adminhtml/session')->addSuccess("Database has been rescanned for faulty product");
            $this->getResponse()->setRedirect($this->_getRefererUrl());;
        } catch (Exception $e) {
            Mage::log("There has been an error in ".__FILE__." with the message ".$e->getMessage());
        }
    }
    
    public function massDeleteAction() {
        $itemIds = $this->getRequest()->getParam('item_id');
        // $this->getMassactionBlock()->setFormFieldName('tax_id'); from Mage_Adminhtml_Block_Tax_Rate_Grid
        if(!is_array($itemIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select tax(es).'));
        }
        else {
            try {
                $rateModel = Mage::getModel('tax/calculation_rate');
                foreach ($itemIds as $itemId) {
                    $rateModel->load($itemId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('tax')->__('Total of %d record(s) were deleted.', count($itemIds)));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }    
}