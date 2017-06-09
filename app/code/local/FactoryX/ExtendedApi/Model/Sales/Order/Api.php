<?php

/**
 * Class FactoryX_ExtendedApi_Model_Sales_Order_Api
 */
class FactoryX_ExtendedApi_Model_Sales_Order_Api extends Mage_Api_Model_Resource_Abstract {
    
    /**
     *
     */
    public function __construct() {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * @param $purchaseOrderNumber
     * @return array
     */
    public function find($purchaseOrderNumber) {
        Mage::helper('extended_api')->log(sprintf("%s->%s", __METHOD__, $purchaseOrderNumber));
        
        $orders = Mage::getModel('sales/order')->getCollection();
        $orders->getSelect()->join(
            array('payment' => $orders->getResource()->getTable('sales/order_payment')),
            'payment.parent_id = main_table.entity_id',
            array()
        );
        $orders->addFieldToFilter('method','purchaseorder');
        $orders->addFieldToFilter('po_number', array(
            //'like' => sprintf("%%%s%%", $purchaseOrderNumber)
            'eq' => $purchaseOrderNumber
        ));
        $result = $orders->getData();
        return $result;
    }

}
