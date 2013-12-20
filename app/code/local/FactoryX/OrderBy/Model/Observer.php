<?php

class FactoryX_OrderBy_Model_Observer extends Mage_Core_Model_Abstract 
{
    // public function __construct()
    // {
    //     $this->_init('orderby/observer');
    // }

    /**
    * Save order by 
    */
    public function saveOrderBy($observer) 
    {
        //$userArray = Mage::getSingleton('admin/session')->getData();
        //Mage::helper('orderby')->log(var_dump($userArray));
        
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getIncrementId();
		// If the order has not been placed from the frontend, the remote IP is null
		if (is_null($order->getRemoteIp()))
		{
			$user = Mage::getSingleton('admin/session');
			$userId = $user->getUser()->getUserId();
			$userUsername = $user->getUser()->getUsername();
			//$order->setData('order_by',$userId);
			$order->setData('created_by',$userUsername);
			$order->save();
			//Mage::helper('orderby')->log(var_dump($userUsername));
			Mage::helper('orderby')->log(__METHOD__ . " " . $userUsername." - ".$userId." placed the order id ".$orderId);        
		}

        //Mage::helper('orderby')->log('okay guys! I will check who placed the order!');

    }

}