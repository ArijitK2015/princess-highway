<?php

/**
 * Class FactoryX_ShippedFrom_Adminhtml_ShippedfromproductController
 */
class FactoryX_ShippedFrom_Adminhtml_ShippedfromproductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/auspost/products');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('factoryx_menu/auspost/products');

        return $this;
    }

    /**
     * Instantiate current product and put it into registry
     *
     * @return FactoryX_ShippedFrom_Model_Account_Product
     * @throws Mage_Core_Exception
     */
    protected function _initProduct()
    {
        /** @var FactoryX_ShippedFrom_Model_Account_Product $product */
        $product = Mage::getModel('shippedfrom/account_product')->load($this->getRequest()->getParam('id'));
        if (!$product->getId()) {
            Mage::throwException($this->__('Specified product does not exist.'));
        }

        Mage::register('current_auspost_product', $product);
        return $product;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('shippedfrom/adminhtml_product'));
        $this->renderLayout();
    }

    /**
     *
     */
    public function deleteAction()
    {
        $productId  = (int) $this->getRequest()->getParam('id');
        if ($productId) {
            try {
                /** @var FactoryX_ShippedFrom_Model_Account_Product $product */
                $product = Mage::getModel('shippedfrom/account_product');
                $product->load($productId);
                $productId = $product->getData('product_id');
                $fromAccountName = Mage::getModel('shippedfrom/account')->load($product->getData('associated_account'))->getName();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__("The product item '%s' has been deleted from '%s'.", $productId, $fromAccountName));
                $product->delete();
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find a product item to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * Product main view
     */
    public function viewAction()
    {
        $this->_viewAction();
    }

    /**
     * Generic product view action
     */
    protected function _viewAction()
    {
        try {
            $product = $this->_initProduct();
            $this->_title($this->__('Australia Post Product'))
                ->_title($this->__('Product #%s', $product->getProductId()));
            $this->_initAction();
            /** @var FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Product_View $block **/
            $block = $this->getLayout()->createBlock('shippedfrom/adminhtml_auspost_product_view');
            $this->_addContent($block);
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * @function         : exportCsvAction
     * @description      : Export data grid to CSV format
     * @params           : null
     * @returns          : array
     */
    public function exportCsvAction()
    {
        $fileName = sprintf("accounts_products-%s.csv", gmdate('YmdHis'));
        $blockName = "shippedfrom/adminhtml_product_grid";
        $grid = $this->getLayout()->createBlock($blockName);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
}