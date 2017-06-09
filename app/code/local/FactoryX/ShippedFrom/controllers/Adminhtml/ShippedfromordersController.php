<?php

/**
 * Class FactoryX_ShippedFrom_Adminhtml_ShippedfromordersController
 */
class FactoryX_ShippedFrom_Adminhtml_ShippedfromordersController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/auspost/orders');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('factoryx_menu/auspost/orders');

        return $this;
    }

    /**
     *
     */
    public function ordersAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('shippedfrom/adminhtml_orders'));
        $this->renderLayout();
    }

    /**
     * @return $this
     */
    public function summaryAction()
    {
        $orderId  = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            /** @var FactoryX_ShippedFrom_Model_Resource_Orders_Collection $orderCollection */
            $orderCollection = Mage::getResourceModel('shippedfrom/orders_collection')
                ->addFieldToFilter('ap_order_id', $orderId)
                ->setPageSize(1)
                ->setCurPage(1);

            if ($orderCollection->getSize()) {
                $order = $orderCollection->getFirstItem();

                try {
                    // get the file locally OR download and store it
                    if ($order->getOrderSummaryLink()
                        && is_file($order->getOrderSummaryLink())) {
                        $file = $order->getOrderSummaryLink();
                    } else {
                        /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Orders $orderRepository */
                        $orderRepository = Mage::getModel('shippedfrom/auspost_shipping_orders');
                        $file = $orderRepository->getSummary($orderId);
                    }

                    if (!is_file($file)
                        || !is_readable($file)) {
                        Mage::throwException(
                            Mage::helper('shippedfrom')->__(
                                "Could not read specified order summary file%s!",
                                (!empty($file) ? sprintf(" '%s'", $file) : "")
                            )
                        );
                    }

                    $this->prepareFileDownloadResponse($file);

                    exit(0);
                    return $this;
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            } else {
                $this->_getSession()->addError(
                    Mage::helper('shippedfrom')->__("The order '%s' does not exist", $orderId)
                );
            }
        } else {
            $this->_getSession()->addError(Mage::helper('shippedfrom')->__('Please choose an order'));
        }

        $this->_redirect('*/*/orders');
    }

    public function payAction()
    {
        $orderId  = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Payments $paymentRepository */
            $paymentRepository = Mage::getModel('shippedfrom/auspost_shipping_payments');
            $paymentRepository->pay($orderId);
        }
    }

    /**
     */
    public function updateAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $this->_getSession()->addError(Mage::helper('shippedfrom')->__('Please choose an order'));
            $this->_redirect('*/*/orders');
        }

        /** @var FactoryX_ShippedFrom_Model_Resource_Orders_Collection $orderCollection */
        $orderCollection = Mage::getResourceModel('shippedfrom/orders_collection')
            ->addFieldToFilter('ap_order_id', $orderId)
            ->setPageSize(1)
            ->setCurPage(1);

        if (!$orderCollection->getSize()) {
            $this->_getSession()->addError(Mage::helper('shippedfrom')->__("The order '%s' does not exist", $orderId));
            $this->_redirect('*/*/orders');
        }

        $order = $orderCollection->getFirstItem();
        $id = $order->getId();
        try {
            $order = Mage::getModel('shippedfrom/auspost_shipping_orders');
            $order->updateOrder($id);
        }
        catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/orders');
    }

    /**
     * @param $file
     */
    protected function prepareFileDownloadResponse($file)
    {
        $contentType = "application/pdf";   // application/pdf | application/force-download
        $contentDisposition = "inline";     // inline | attachment
        
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType)
            ->setHeader('Content-Length', filesize($file), true)
            ->setHeader('Content-Disposition', $contentDisposition . '; filename="' . basename($file) . '"', true);

        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
        $ioAdapter->streamOpen($file, 'r');
        while ($buffer = $ioAdapter->streamRead()) {
            print $buffer;
        }

        $ioAdapter->streamClose();
    }
}