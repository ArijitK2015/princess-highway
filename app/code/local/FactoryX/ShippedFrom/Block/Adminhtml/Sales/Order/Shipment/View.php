<?php
/**
 * Order Shipment view


 Mage_Adminhtml_Block_Widget_Form_Container
 Mage_Adminhtml_Block_Sales_Order_Shipment_View

 * @category   Mage
 * @package    FactoryX_ShippedFrom
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Sales_Order_Shipment_View {

    /**
    */
    public function  __construct() {
        parent::__construct();

        $storeId = $this->getShipment()->getShippedFrom();
        if (!$storeId) {
            return;
        }
        /*
        if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_View' && $block->getRequest()->getControllerName() == 'sales_order') {
            return;
        }
        */

        $store = Mage::helper('shippedfrom')->getStore($storeId);

        $prompt = sprintf("Are you sure you want to send the Packing Slip to \'%s\'?", $store->getTitle());
        //$email = sprintf("%s@factoryx.com.au", $store->getStoreCode());
        $email = Mage::helper('shippedfrom')->getStoreEmail($store);
        $url = $this->getEmailPdfUrl();

        // _addButton is a protected method, better to use the public addButton
        $this->_addButton('send_pdf_to_store', array(
            'label'     => 'Send Packing Slip To Store',
            //'onclick'   => "confirmSetLocation('{$message}', '{$url}')",
            'onclick'   => "var email = prompt('{$prompt}', '{$email}');
                if (email != null) {
                    setLocation('{$url}' + '?email=' + email);
                }
                return false;",
            'class'     => 'save' // save | go ...
        ), 0, 100, 'header', 'header');
    }

    /**    
    */
    public function getEmailPdfUrl() {
        //$url = $this->getUrl('*/sales_order_shipment/emailStore', array('shipment_id'  => $this->getShipment()->getId()));
        
        // FactoryX_ShippedFrom_Sales_Order_ShipmentController
        $url = Mage::helper("adminhtml")->getUrl('*/sales_order_shipment/emailStore', array('shipment_id'  => $this->getShipment()->getId()));
                
        // FactoryX_ShippedFrom_Adminhtml_Sales_Order_ShipmentController
        //$url = Mage::helper("adminhtml")->getUrl('*/adminhtml_sales_order_shipment/emailStore', array('shipment_id'  => $this->getShipment()->getId()));
        
        $prompt = Mage::helper('shippedfrom')->log(sprintf("%s->url: %s", __METHOD__, $url));
        return $url;
    }

}