<?php

class FactoryX_ReviewNotification_Adminhtml_ReviewnotificationController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view');
    }

    /**
     * Manually send email
     */
    public function sendOrderEmailAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
            
        try
        {
            $orderId = trim($orderId);
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId())
                    throw new Exception($this->__('Order does not exist !'));

            Mage::helper('reviewnotification/notifier')->notify($order);

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Email sent'));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
        }

        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }

    /**
     * Send test email
     */
    public function sendTestAction()
    {
        try
        {
            $orderId = $this->getRequest()->getParam('order_id');
            $orderId = trim($orderId);
            $order = Mage::getModel('sales/order')->load($orderId, 'increment_id');
            if (!$order->getId())
                    throw new Exception($this->__('Order #%s does not exist !', $orderId));

            Mage::helper('reviewnotification/notifier')->notify($order);

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Test email sent'));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
        }

        $this->_redirect('adminhtml/system_config/edit', array('section' => 'reviewnotification'));
    }

}