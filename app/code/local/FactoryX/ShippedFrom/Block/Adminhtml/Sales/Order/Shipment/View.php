<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Sales_Order_Shipment_View
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Sales_Order_Shipment_View
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_View
{

    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_Sales_Order_Shipment_View constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $storeId = $this->getShipment()->getShippedFrom();
        if (!$storeId) {
            return;
        }

        $store = Mage::helper('shippedfrom')->getStore($storeId);

        $prompt = Mage::helper('shippedfrom')->__(
            "Are you sure you want to send the Packing Slip to \'%s\'?",
            $store->getTitle()
        );
        $email = Mage::helper('shippedfrom')->getStoreEmail($store);
        $url = $this->getEmailPdfUrl();

        $this->addButton(
            'send_pdf_to_store',
            array(
                'label'     => 'Send Packing Slip To Store',
                'onclick'   => "var email = prompt('{$prompt}', '{$email}');
                    if (email != null) {
                        setLocation('{$url}' + '?email=' + email);
                    }
                    return false;",
                'class'     => 'save'
            ),
            0,
            100,
            'header'
        );
    }

    /**
     * @return mixed
     */
    protected function getEmailPdfUrl()
    {
        $url = Mage::helper("adminhtml")->getUrl(
            '*/sales_order_shipment/emailStore',
            array(
                'shipment_id'  => $this->getShipment()->getId()
            )
        );
        return $url;
    }

}