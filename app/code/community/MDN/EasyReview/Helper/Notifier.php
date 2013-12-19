<?php

class MDN_EasyReview_Helper_Notifier extends Mage_Core_Helper_Abstract {

    /**
     * Notify new orders
     */
    public function run() {
        //get orders
        $orders = $this->getOrders();

        $count = 0;

        //notify customers
        foreach ($orders as $order) {
            $this->notify($order);
            $count++;
        }

        return $count;
    }

    /**
     * Return orders
     */
    protected function getOrders() {
        $collection = Mage::getModel('sales/order')->getCollection();

        //add consider from date condition
        $considerOrdersAfter = Mage::getStoreConfig('easyreview/general/consider_orders_after');
        if ($considerOrdersAfter) {
            $collection->addFieldToFilter('created_at', array('gt' => $considerOrdersAfter));
        }

        //add delay condition on complete status
        $delay = Mage::getStoreConfig('easyreview/general/notify_delay');
        $collection->addFieldToFilter('state', 'complete');
        $collection->addFieldToFilter('updated_at', array('lt' => date('Y-m-d', time() - $delay * 24 * 3600)));

        //add not notifed condition
        $collection->addFieldToFilter('easyreview_notified', 0);

        //return
        return $collection;
    }

    /**
     * Notify order
     * @param <type> $order
     */
    public function notify($order) {

        $hashCode = md5($order->getincrement_id());

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
        Mage::app()->getLocale()->emulate($order->getStoreId());

        $templateId = Mage::getStoreConfig('easyreview/general/email_template', $order->getStoreId());
        $identityId = Mage::getStoreConfig('easyreview/general/email_identity', $order->getStoreId());

        $customerEmail = $order->getCustomerEmail();

        //set data to use in array
        $data = $order->getData();
        $data['customer_name'] = $order->getcustomer_firstname().' '.$order->getcustomer_lastname();
        $data['direct_url'] = Mage::getUrl('EasyReview/Front/PostReviews', array('_store' => $order->getStoreId(), 'security_key' => $hashCode));
        $data['order_date'] = Mage::helper('core')->formatDate($order->getcreated_at(), 'medium', false);

        //send email
        Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'adminhtml', 'store' => $order->getStoreId()))
                ->sendTransactional(
                        $templateId,
                        $identityId,
                        $customerEmail,
                        $order->getcustomer_firstname().' '.$order->getcustomer_lastname(),
                        $data,
                        null);

        $translate->setTranslateInline(true);

        //save hash code and notified in order
        $order->seteasyreview_hashcode($hashCode)
                ->seteasyreview_notified(1)
                ->seteasyreview_date(date('Y-m-d'))
                ->save();

        Mage::app()->getLocale()->revert();

        return $this;
    }

    /**
     * Return order from security code
     * @param <type> $securityCode
     */
    public function getOrder($securityCode)
    {
        $order = Mage::getModel('sales/order')->load($securityCode, 'easyreview_hashcode');
        if (!$order->getId())
                throw new Exception(Mage::helper('EasyReview')->__('Unable to load order'));
        return $order;
    }

}