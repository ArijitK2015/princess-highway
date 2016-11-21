<?php

class FactoryX_ReviewNotification_Helper_Notifier extends Mage_Core_Helper_Abstract
{

    /**
     * Notify new orders
     */
    public function run()
    {
        $orders = $this->getOrders();
        $count = 0;

        foreach ($orders as $order) {
            try {
                $this->notify($order);
                $count++;
            } catch (Exception $e) {
                Mage::helper('reviewnotification')->log($e->getMessage());
                continue;
            }
        }

        return $count;
    }

    /**
     * Return orders
     * @return Mage_Sales_Model_Resource_Order_Collection $collection
     */
    protected function getOrders()
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getModel('sales/order')->getCollection();

        $considerOrdersAfter = Mage::getStoreConfig('reviewnotification/general/consider_orders_after');
        if ($considerOrdersAfter) {
            $collection->addFieldToFilter('created_at', array('gt' => $considerOrdersAfter));
        }

        $delay = Mage::getStoreConfig('reviewnotification/general/notify_delay');
        $collection->addFieldToFilter('state', 'complete');
        $collection->addFieldToFilter('updated_at', array('lt' => date('Y-m-d', time() - $delay * 24 * 3600)));

        $collection->addFieldToFilter('easyreview_notified', 0);

        return $collection;
    }

    /**
     * Notify order
     * @param Mage_Sales_Model_Order $order
     */
    public function notify(Mage_Sales_Model_Order $order)
    {
        if ($order->getState() != Mage_Sales_Model_Order::STATE_COMPLETE) {
            throw new Exception(Mage::helper('reviewnotification')->__('Cannot notify order %s as it is not complete', $order->getIncrementId()));
            return $this;
        }

        $hashCode = md5($order->getIncrementId());

        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        Mage::app()->getLocale()->emulate($order->getStoreId());

        $templateId = Mage::getStoreConfig('reviewnotification/general/email_template', $order->getStoreId());
        $identityId = Mage::getStoreConfig('reviewnotification/general/email_identity', $order->getStoreId());

        $customerEmail = $order->getCustomerEmail();

        //set data to use in array
        $data = $order->getData();
        $data['customer_name'] = sprintf("%s %s", $order->getcustomer_firstname(), $order->getcustomer_lastname());
        // not sure why a helper wasn't used ???
        //$data['direct_url'] = Mage::getUrl('EasyReview/Front/PostReviews', array('_store' => $order->getStoreId(), 'security_key' => $hashCode));
        $data['direct_url'] = Mage::helper('reviewnotification')->getDirectUrl($order->getId());
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
     * @param string $hashcode
     * @return Mage_Sales_Model_Order $order
     */
    public function getOrderByHashcode($hashcode)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($hashcode, 'easyreview_hashcode');
        if (!$order->getId()) {
            throw new Exception(Mage::helper('reviewnotification')->__('Unable to load order'));
        }
        return $order;
    }

}