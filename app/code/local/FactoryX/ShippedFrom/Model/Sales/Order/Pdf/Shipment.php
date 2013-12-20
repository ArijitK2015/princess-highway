<?php
/**
 * Add the ShippedFrom box to the shipment PDf
 * Sales Order Shipment PDF model
 *
 * @category   Mage
 * @package    FactoryX_Sales
 * @author     Factory X Team <raphael@factoryx.com.au>
 */
class FactoryX_ShippedFrom_Model_Sales_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{
    /**
     * Return PDF document
     *
     * @param  array $shipments
     * @return Zend_Pdf
     */
    public function getPdf($shipments = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId()
            );

			/*** START Add Shipped From Box ***/
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawText("Shipped From:", 405, 780,'UTF-8');
			$shippingStoreId = $shipment->getShippedFrom(); 
			$shippingStore = Mage::getModel('ustorelocator/location')->load($shippingStoreId);
			if ($shippingStore != null) 
			{
				$page->drawText($shippingStore->getTitle(), 470, 780,'UTF-8');
			}
			
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
			/*** END Add Shipped From Box ***/
			
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }
}
