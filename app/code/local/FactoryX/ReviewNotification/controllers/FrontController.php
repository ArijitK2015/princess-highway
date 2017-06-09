<?php

class FactoryX_ReviewNotification_FrontController extends Mage_Core_Controller_Front_Action {

    public function getCurrentOrder() {
        Mage::log(sprintf("%s->var=%s", __METHOD__, print_r($this->getRequest()->getParams(), true)) );
    }

    /**
     * Display post review page
     */
    public function postReviewsAction() {
        try {

            //get order from security code
            $security_key = $this->getRequest()->getParam('security_key');
            $helper = Mage::helper('reviewnotification/notifier');
            $order = $helper->getOrderByHashcode($security_key);
            Mage::app()->getLocale()->emulate($order->getStoreId());

            //log in customer
            $customerId = $order->getcustomer_id();
            Mage::helper('reviewnotification/login')->logCustomer($customerId);

            //display form
            Mage::register('reviewnotification_current_order', $order);
            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $ex) {
            Mage::getSingleton('customer/session')->addError($ex->getMessage());
            $this->_redirect('customer/account/login');
        }
    }

    /**
     * Submit reviews for approval
     */
    public function saveReviewAction() {

        try {

            $customerId = Mage::Helper('customer')->getCustomer()->getId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $ratings = $this->getRequest()->getPost('ratings');
            $orderId = $this->getRequest()->getPost('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);
            $customerName = $order->getcustomer_firstname().' '.$order->getcustomer_lastname();
            if (trim($customerName) == '')
            {
                $billingAddress = $order->getBillingAddress();
                $customerName = $billingAddress->getfirstname().' '.$billingAddress->getlastname();
            }

            
            foreach($ratings as $productId => $info)
            {
                $review = Mage::getModel('review/review')->setData($data);
                $review->setTitle($info['title']);
                $review->setDetail($info['detail']);
                $review->setnickname($customerName);
                $validate = $review->validate();
                $product = Mage::getModel('catalog/product')->load($productId);

                $review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
                    ->setEntityPkValue($product->getId())
                    ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
                    ->setCustomerId($customerId)
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->setStores(array(Mage::app()->getStore()->getId()))
                    ->save();

                foreach ($info['notes'] as $ratingId => $optionId) {
                    Mage::getModel('rating/rating')
                    ->setRatingId($ratingId)
                    ->setReviewId($review->getId())
                    ->setCustomerId($customerId)
                    ->addOptionVote($optionId, $product->getId());
                }

                $review->aggregate();
            }

            Mage::getSingleton('customer/session')->addSuccess($this->__('Reviews have been submitted'));
        } catch (Exception $ex) {
            Mage::getSingleton('customer/session')->addError($this->__('An error happened : %s', $ex->getMessage()));
        }

        $this->_redirect('customer/account/index');
    }

}