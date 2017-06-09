<?php
/**
 * Sales Order Pdf grouped items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class FireGento_Pdf_Model_Items_Order_Grouped extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    /**
     * Draw process
     */
    public function draw()
    {
        //Mage::helper('firegento_pdf')->log(sprintf("%s->getItem(): %s", __METHOD__, print_r($this->getItem(), true)) );
        
        $type = $this->getItem()->getOrderItem()->getRealProductType();
        $renderer = $this->getRenderedModel()->getRenderer($type);
        $renderer->setOrder($this->getOrder());
        $renderer->setItem($this->getItem());
        $renderer->setPdf($this->getPdf());
        $renderer->setPage($this->getPage());

        $renderer->draw();
    }
}
