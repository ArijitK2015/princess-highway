<?php
/**
firegento_pdf/gst
*/
class FireGento_Pdf_Model_Tax_Sales_Pdf_Gst extends Mage_Sales_Model_Order_Pdf_Total_Default {

    const DEFAULT_GST_TAX_RATE = 10; // percentage

    /**
     * calculate gst
     *
     * @return float gst
     */
    public function getAmount() {
        $rate = ($this->getGstRate() / 100) + 1;
        $exGstTotal = $this->getOrder()->getGrandTotal() / $rate;
        return $this->getOrder()->getGrandTotal() - $exGstTotal;
    }

    /**
     * check if there is a rate with code gst
     *
     * @return float rate
     */
    public function getGstRate() {
        $rate = self::DEFAULT_GST_TAX_RATE;
        $gstTaxRate = Mage::getSingleton('tax/calculation_rate')->loadByCode('gst');
        if ($gstTaxRate && is_numeric($gstTaxRate->getRate())) {
            $rate = $gstTaxRate->getRate();
        }
        return $rate;
    }

    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay() {
        $totals = array();
        $store = $this->getOrder()->getStore();
        $helper = Mage::helper('tax');
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        //Mage::helper('firegento_pdf')->log(sprintf("%s->amount=%s", __METHOD__, $this->getAmount()) );

        if (Mage::helper('firegento_pdf')->getShowGst()) {
            if (Mage::helper('firegento_pdf')->showGst($this->getOrder()->getBillingAddress()->getCountryId()) ) {
                $totals[] = array(
                    'amount'    => $this->getOrder()->formatPriceTxt($this->getAmount()),
                    'label'     => "GST:",
                    'font_size' => $fontSize
                );
            }
        }
        return $totals;
    }
}
