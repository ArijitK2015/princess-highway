<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Shipment
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Shipment
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renderer for the action column
     * @param Varien_Object $row
     * @return string
     */    
    public function render(Varien_Object $row)
    {
        $retVal = "";
        $incrementId = $row->getData($this->getColumn()->getIndex());
        if (preg_match("/^[0-9]{9}$/", $incrementId)) {
            $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($incrementId, 'increment_id');
            $shipmentId = $shipment->getId();
        } elseif (is_numeric($incrementId)) {
            $shipmentId = $incrementId;
        } else {
            $shipmentId = false;
        }

        if ($shipmentId) {
            $shipmentUrl = $this->helper('adminhtml')->getUrl(
                'adminhtml/sales_shipment/view',
                array('shipment_id' => $shipmentId)
            );
            $retVal = sprintf("<a href='%s'>%s</a>", $shipmentUrl, $incrementId);
        }

        return $retVal;
    }
}