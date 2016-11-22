<?php
/**
 * Order model rewrite.
 *
 * The order model serves as a proxy to the actual PDF engine as set via
 * backend configuration.
 *
 * @category  FireGento
 * @package   FireGento_Pdf
 * @author    FireGento Team <team@firegento.com>
 */
class FireGento_Pdf_Model_Order
{
    /**
     * The actual PDF engine responsible for rendering the file.
     *
     * @var Mage_Sales_Model_Order_Pdf_Abstract
     */
    private $_engine;

    /**
     * get pdf rendering engine
     *
     * @return Mage_Sales_Model_Order_Pdf_Abstract | FireGento_Pdf_Model_Order_Pdf_Order
     */
    protected function getEngine()
    {
        if (!$this->_engine) {
            // TODO: this needs to be configured
            //$modelClass = Mage::getStoreConfig('sales_pdf/order/engine');
            $modelClass = "firegento_pdf/engine_order_default";
            //Mage::helper('firegento_pdf')->log(sprintf("%s->modelClass=%s", __METHOD__, $modelClass));
            $engine = Mage::getModel($modelClass);

            if (!$engine) {
                // Fallback to Magento standard order layout
                //$engine = new Mage_Sales_Model_Order_Pdf_Abstract();
                $engine = new FireGento_Pdf_Model_Order_Pdf_Order();
            }

            $this->_engine = $engine;
        }
        //Mage::helper('firegento_pdf')->log(sprintf("%s->_engine=%s", __METHOD__, get_class($this->_engine)));
        return $this->_engine;
    }

    /**
     * get pdf for orders
     *
     * @param  array | Varien_Data_Collection $orders orders to render pdfs for
     *
     * @return mixed
     */
    public function getPdf($orders = array())
    {
        return $this->getEngine()->getPdf($orders);
    }

}
