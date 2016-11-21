<?php

/**
 * Class FactoryX_OrderBy_Model_Observer
 */
class FactoryX_OrderBy_Model_Observer extends Mage_Core_Model_Abstract {

    /**
     * Save order by
     * @param $observer
     */
    public function saveOrderBy($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getIncrementId();

        Mage::helper('orderby')->log(sprintf("%s->fired '%s' for orderId '%s'", __METHOD__, $observer->getEvent()->getName(), $orderId) );

        Mage::helper('orderby')->log(sprintf("%s->check: %d|%d|%s", __METHOD__,
            (empty($order->getData('remote_ip')) || $this->_fromApi()),
            empty($order->getData('created_by')),
            (strtotime($order->getData('created_at')) === strtotime($order->getData('updated_at')))
        ));

        //Mage::helper('orderby')->log(sprintf("%s->order: %s", __METHOD__, print_r($order->getData(), true)) );
        if (
            (
                // no remote IP order has 'not' been placed from the frontend
                empty($order->getData('remote_ip'))
                ||
                $this->_fromApi()
            )
            &&
            empty($order->getData('created_by'))
            &&
            // just created
            (strtotime($order->getData('created_at')) === strtotime($order->getData('updated_at')))
        ) {

            if ($this->_fromApi()) {
                $session = Mage::getSingleton('api/session');
            }
            else {
                $session = Mage::getSingleton('admin/session');
            }
            
            $userId = $session->getUser()->getUserId();
            $userUsername = $session->getUser()->getUsername();
            //$order->setData('order_by',$userId);
            $order->setData('created_by', $userUsername);
            $order->getResource()->saveAttribute($order, 'created_by');
            //Mage::helper('orderby')->log(var_dump($userUsername));
            Mage::helper('orderby')->log(sprintf("%s->user %s[%s] placed the order id %s", __METHOD__, $userUsername, $userId, $orderId));
        }
        Mage::helper('orderby')->log(sprintf("%s->done", __METHOD__) );
    }

    /**
    */
    private function _fromApi() {
        $fromApi = (
            preg_match("/soap/i", Mage::app()->getRequest()->getControllerName())
            &&
            preg_match("/api/i", Mage::app()->getRequest()->getRouteName())
        );
        Mage::helper('orderby')->log(sprintf("%s->fromApi: %d", __METHOD__, $fromApi) );
        return $fromApi;
    }

}