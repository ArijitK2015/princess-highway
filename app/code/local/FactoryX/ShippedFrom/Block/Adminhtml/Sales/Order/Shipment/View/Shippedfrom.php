<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Sales_Order_Shipment_View_Shippedfrom
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Sales_Order_Shipment_View_Shippedfrom extends Mage_Adminhtml_Block_Template
{

    /**
     * Prepares layout of block
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('shipment[shipped_from]').parentNode, '".$this->getSubmitUrl()."')";
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('shippedfrom')->__('Update Store'),
                        'class'   => 'save',
                        'onclick' => $onclick
                    )
                )
        );

        return $this;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Retrieve save url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/updateStore/', array('shipment_id'=>$this->getShipment()->getId()));
    }

    /**
     * Retrive save button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
}