<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_Pdf
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2014 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * Dummy data helper for translation issues.
 *
 * @category  FireGento
 * @package   FireGento_Pdf
 * @author    FireGento Team <team@firegento.com>
 */
class FireGento_Pdf_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $logFileName = 'firegento_pdf.log';
    
    const XML_PATH_FIREGENTO_PDF_LOGO_POSITION = 'sales_pdf/firegento_pdf/logo_position';
    const XML_PATH_SALES_PDF_INVOICE_SHOW_CUSTOMER_NUMBER = 'sales_pdf/invoice/show_customer_number';
    const XML_PATH_SALES_PDF_SHIPMENT_SHOW_CUSTOMER_NUMBER = 'sales_pdf/shipment/show_customer_number';
    const XML_PATH_SALES_PDF_CREDITMEMO_SHOW_CUSTOMER_NUMBER = 'sales_pdf/creditmemo/show_customer_number';
    const XML_PATH_SALES_PDF_INVOICE_SHOW_CUSTOMER_VATNUMBER = 'sales_pdf/invoice/show_customer_vatnumber';
    const XML_PATH_SALES_PDF_SHIPMENT_SHOW_CUSTOMER_VATNUMBER = 'sales_pdf/shipment/show_customer_vatnumber';
    // ...
    const XML_PATH_SALES_PDF_SHIPMENT_SHOW_ADDRESS_TITLES = 'sales_pdf/shipment/show_address_titles';
    const XML_PATH_SALES_PDF_CREDITMEMO_SHOW_CUSTOMER_VATNUMBER = 'sales_pdf/creditmemo/show_customer_vatnumber';
    //const XML_PATH_SALES_PDF_ORDER_FILENAME_EXPORT_PATTERN = 'sales_pdf/order/filename_export_pattern';
    const XML_PATH_SALES_PDF_ORDER_FILENAME_EXPORT_PATTERN = '%Y%m%d_order_{{order_id}}_{{customer_firstname}}_{{customer_lastname}}';
    const XML_PATH_SALES_PDF_INVOICE_FILENAME_EXPORT_PATTERN = 'sales_pdf/invoice/filename_export_pattern';
    const XML_PATH_SALES_PDF_SHIPMENT_FILENAME_EXPORT_PATTERN = 'sales_pdf/shipment/filename_export_pattern';
    const XML_PATH_SALES_PDF_CREDITMEMO_FILENAME_EXPORT_PATTERN = 'sales_pdf/creditmemo/filename_export_pattern';
    const XML_PATH_SALES_PDF_INVOICE_FILENAME_EXPORT_PATTERN_FOR_MULTIPLE_DOCUMENTS = 'sales_pdf/invoice/filename_export_pattern_for_multiple_documents';
    const XML_PATH_SALES_PDF_SHIPMENT_FILENAME_EXPORT_PATTERN_FOR_MULTIPLE_DOCUMENTS = 'sales_pdf/shipment/filename_export_pattern_for_multiple_documents';
    const XML_PATH_SALES_PDF_CREDITMEMO_FILENAME_EXPORT_PATTERN_FOR_MULTIPLE_DOCUMENTS = 'sales_pdf/creditmemo/filename_export_pattern_for_multiple_documents';
    const XML_PATH_SALES_PDF_FIREGENTO_PDF_PAGE_SIZE = 'sales_pdf/firegento_pdf/page_size';

    const XML_PATH_TAX_SALES_DISPLAY_TAX_MESSAGE = 'tax/sales_display/tax_message';
    const XML_PATH_TAX_SALES_DISPLAY_SHOW_GST = 'tax/sales_display/show_gst';
    const XML_PATH_TAX_SALES_DISPLAY_SPECIFICCOUNTRY = 'tax/sales_display/specificcountry';

    const XML_PATH_REGULAR_FONT = 'sales_pdf/firegento_pdf_fonts/regular_font';
    const XML_PATH_BOLD_FONT = 'sales_pdf/firegento_pdf_fonts/bold_font';
    const XML_PATH_ITALIC_FONT = 'sales_pdf/firegento_pdf_fonts/italic_font';

    const FONT_PATH_IN_MEDIA = '/firegento_pdf/fonts';

    /**
     * Return the order id or false if order id should not be displayed on document.
     *
     * @param  Mage_Sales_Model_Order $order order to get id from
     * @param  string                 $mode  differ between creditmemo, invoice, etc.
     *
     * @return mixed
     */
    public function putOrderId(Mage_Sales_Model_Order $order, $mode = 'invoice')
    {
        switch ($mode) {
            case 'order':
                return false;
                
            case 'invoice':
                $putOrderIdOnInvoice = Mage::getStoreConfigFlag(
                    Mage_Sales_Model_Order_Pdf_Abstract::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    $order->getStoreId()
                );
                if ($putOrderIdOnInvoice) {
                    return $order->getRealOrderId();
                }
                break;

            case 'shipment':
                $putOrderIdOnShipment = Mage::getStoreConfigFlag(
                    Mage_Sales_Model_Order_Pdf_Abstract::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID,
                    $order->getStoreId()
                );
                if ($putOrderIdOnShipment) {
                    return $order->getRealOrderId();
                }
                break;

            case 'creditmemo':
                $putOrderIdOnCreditmemo = Mage::getStoreConfigFlag(
                    Mage_Sales_Model_Order_Pdf_Abstract::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID,
                    $order->getStoreId()
                );
                if ($putOrderIdOnCreditmemo) {
                    return $order->getRealOrderId();
                }
                break;
        }

        return false;
    }

    /**
     * Whether the logo should be shown in full width.
     *
     * @param  mixed $store store to get information from
     *
     * @return bool whether the logo should be shown in full width
     */
    public function isLogoFullWidth($store)
    {
        $configSetting = Mage::getStoreConfig(
            self::XML_PATH_FIREGENTO_PDF_LOGO_POSITION, $store
        );
        $fullWidth = FireGento_Pdf_Model_System_Config_Source_Logo::FULL_WIDTH;

        return $configSetting == $fullWidth;
    }

    /**
     * Whether the customer number should be shown.
     *
     * @param  string $mode  the mode of this document like invoice, shipment or creditmemo
     * @param  mixed  $store store to get information from
     *
     * @return bool whether the customer number should be shown
     */
    public function showCustomerNumber($mode = 'invoice', $store) {
        switch ($mode) {
            case 'order':
            case 'invoice':
                return Mage::getStoreConfigFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_SHOW_CUSTOMER_NUMBER,
                    $store
                );
            case 'shipment':
                return Mage::getStoreConfigFlag(
                    self::XML_PATH_SALES_PDF_SHIPMENT_SHOW_CUSTOMER_NUMBER,
                    $store
                );
            case 'creditmemo':
                return Mage::getStoreConfigFlag(
                    self::XML_PATH_SALES_PDF_CREDITMEMO_SHOW_CUSTOMER_NUMBER,
                    $store
                );
        }
        return true; // backwards compatibility
    }

    /**
     *
     * @param  string $mode  the mode of this document like invoice, shipment or creditmemo
     * @param  mixed  $store store to get information from
     *
     * @return bool whether the customer number should be shown
     */
    public function showAddressTitles($mode = 'invoice', $store) {
        return Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_SHOW_ADDRESS_TITLES, $store);
        /*
        switch ($mode) {
            case 'order':        
            case 'invoice':
                return Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_SHOW_CUSTOMER_NUMBER, $store);
            case 'shipment':
                return Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_SHOW_CUSTOMER_NUMBER, $store);
            case 'creditmemo':
                return Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_CREDITMEMO_SHOW_CUSTOMER_NUMBER, $store);
        }
        return true; // backwards compatibility
        */
    }

    /**
     * Whether the customer VAT number should be shown.
     *
     * @param  string $mode  the mode of this document like invoice, shipment or creditmemo
     * @param  mixed  $store store to get information from
     *
     * @return bool whether the customer number should be shown
     */
    public function showCustomerVATNumber($mode = 'invoice', $store)
    {
        switch ($mode) {
            case 'order':            
            case 'invoice':
                return Mage::getStoreConfigFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_SHOW_CUSTOMER_VATNUMBER,
                    $store
                );
            case 'shipment':
                return Mage::getStoreConfigFlag(
                    self::XML_PATH_SALES_PDF_SHIPMENT_SHOW_CUSTOMER_VATNUMBER,
                    $store
                );
            case 'creditmemo':
                return Mage::getStoreConfigFlag(
                    self::XML_PATH_SALES_PDF_CREDITMEMO_SHOW_CUSTOMER_VATNUMBER,
                    $store
                );
        }

        return true; // backwards compatibility
    }

    /**
     * Return scaled image sizes based on an path to an image file.
     *
     * @param  string $image     Url to image file.
     * @param  int    $maxWidth  max width the image can have
     * @param  int    $maxHeight max height the image can have
     *
     * @return array  with 2 elements - width and height.
     */
    public function getScaledImageSize($image, $maxWidth, $maxHeight)
    {
        list($width, $height) = getimagesize($image);

        if ($height > $maxHeight or $width > $maxWidth) {
            // Calculate max variance to match dimensions.
            $widthVar = $width / $maxWidth;
            $heightVar = $height / $maxHeight;

            // Calculate scale factor to match dimensions.
            if ($widthVar > $heightVar) {
                $scale = $maxWidth / $width;
            } else {
                $scale = $maxHeight / $height;
            }

            // Calculate new dimensions.
            $height = round($height * $scale);
            $width = round($width * $scale);
        }

        return array($width, $height);
    }

    /**
     * Return export pattern config value
     *
     * @param  string $type the type of this document like invoice, shipment or creditmemo
     *
     * @return string
     */
    public function getExportPattern($type)
    {
        switch ($type) {
            case 'order':
                return self::XML_PATH_SALES_PDF_ORDER_FILENAME_EXPORT_PATTERN;
                //return Mage::getStoreConfig(self::XML_PATH_SALES_PDF_ORDER_FILENAME_EXPORT_PATTERN);
            case 'invoice':
                return Mage::getStoreConfig(
                    self::XML_PATH_SALES_PDF_INVOICE_FILENAME_EXPORT_PATTERN
                );
            case 'shipment':
                return Mage::getStoreConfig(
                    self::XML_PATH_SALES_PDF_SHIPMENT_FILENAME_EXPORT_PATTERN
                );
            case 'creditmemo':
                return Mage::getStoreConfig(
                    self::XML_PATH_SALES_PDF_CREDITMEMO_FILENAME_EXPORT_PATTERN
                );
        }

        return true;
    }

    /**
     * Return export pattern for multiple documents config value
     *
     * @param  string $type the type of this document like invoice, shipment or creditmemo
     *
     * @return string
     */
    public function getExportPatternForMultipleDocuments($type)
    {
        switch ($type) {
            case 'order':
            case 'invoice':
                return Mage::getStoreConfig(
                    self::XML_PATH_SALES_PDF_INVOICE_FILENAME_EXPORT_PATTERN_FOR_MULTIPLE_DOCUMENTS
                );
            case 'shipment':
                return Mage::getStoreConfig(
                    self::XML_PATH_SALES_PDF_SHIPMENT_FILENAME_EXPORT_PATTERN_FOR_MULTIPLE_DOCUMENTS
                );
            case 'creditmemo':
                return Mage::getStoreConfig(
                    self::XML_PATH_SALES_PDF_CREDITMEMO_FILENAME_EXPORT_PATTERN_FOR_MULTIPLE_DOCUMENTS
                );
        }

        return true;
    }

    /**
     * Gets the variables which can be used as a placeholder in the filename.
     *
     * @param  Mage_Core_Model_Abstract $model the model instance
     *
     * @return array with the variables which can be use as placeholders in the filename
     */
    public function getModelVars($model)
    {
        if (!$model instanceof Mage_Sales_Model_Order) {
            switch ($model) {
                case $model instanceof Mage_Sales_Model_Order_Invoice:
                    $specificVars = array(
                        '{{invoice_id}}' => $model->getIncrementId()
                    );
                    break;
                case $model instanceof Mage_Sales_Model_Order_Shipment:
                    $specificVars = array(
                        '{{shipment_id}}' => $model->getIncrementId()
                    );
                    break;
                case $model instanceof Mage_Sales_Model_Order_Creditmemo:
                    $specificVars = array(
                        '{{creditmemo_id}}' => $model->getIncrementId()
                    );
            }
            $order = $model->getOrder();
            $commonVars = array(
                '{{order_id}}'           => $order->getIncrementId(),
                '{{customer_id}}'        => $order->getCustomerId(),
                '{{customer_name}}'      => $order->getCustomerName(),
                '{{customer_firstname}}' => $order->getCustomerFirstname(),
                '{{customer_lastname}}'  => $order->getCustomerLastname()
            );

            return array_merge($specificVars, $commonVars);
        } else {
            return array(
                '{{order_id}}'           => $model->getIncrementId(),
                '{{customer_id}}'        => $model->getCustomerId(),
                '{{customer_name}}'      => $model->getCustomerName(),
                '{{customer_firstname}}' => $model->getCustomerFirstname(),
                '{{customer_lastname}}'  => $model->getCustomerLastname()
            );
        }
    }

    /**
     * The filename of the exported file.
     *
     * @param  string                   $type  the type of this document like invoice, shipment or creditmemo
     * @param  Mage_Core_Model_Abstract $model the model instance
     *
     * @return string the filename of the exported file
     */
    public function getExportFilename($type, $model)
    {
        $type = (!$type) ? 'invoice' : $type;
        $pattern = $this->getExportPattern($type);
        //$this->log(sprintf("pattern[%s]: %s", $type, $pattern));
        if (!$pattern) {
            $date = Mage::getSingleton('core/date');
            $pattern = $type . $date->date('Y-m-d_H-i-s');
        }
        if (substr($pattern, -4) != '.pdf') {
            $pattern = $pattern . '.pdf';
        }

        $path = strftime($pattern, strtotime($model->getCreatedAt()));
        $vars = $this->getModelVars($model);

        return strtr($path, $vars);
    }

    /**
     * The filename of the exported file if multiple documents are printed at once.
     *
     * @param string $type the type of this document like invoice, shipment or creditmemo
     *
     * @return string the filename of the exported file
     */
    public function getExportFilenameForMultipleDocuments($type)
    {
        $type = (!$type) ? 'invoice' : $type;
        $pattern = $this->getExportPatternForMultipleDocuments($type);
        if (!$pattern) {
            $date = Mage::getSingleton('core/date');
            $pattern = $type . $date->date('Y-m-d_H-i-s');
        }
        if (substr($pattern, -4) != '.pdf') {
            $pattern = $pattern . '.pdf';
        }

        return strftime($pattern);
    }

    /**
     * Returns the path where the fonts reside.
     *
     * @return string the path where the fonts reside
     */
    public function getFontPath()
    {
        return Mage::getBaseDir('media') . self::FONT_PATH_IN_MEDIA;
    }

    /**
            Millimetres	    Inches
     	    Width	Length	Width	Length
    A4	    210.0	297.0	8.26	11.69   595:842:
    Letter	215.9	279.4	8.50	11.00   612:792:
    */
    public function getPageSizeConfigPath() {
        return Mage::getStoreConfig(self::XML_PATH_SALES_PDF_FIREGENTO_PDF_PAGE_SIZE);
    }
    

    /*    
    */
    public function getShowGst() {
        return Mage::getStoreConfig(self::XML_PATH_TAX_SALES_DISPLAY_SHOW_GST);
    }

    /*    
    */
    public function getTaxMessage() {
        return Mage::getStoreConfig(self::XML_PATH_TAX_SALES_DISPLAY_TAX_MESSAGE);
    }

    /*    
    */
    public function getDocumentTitle($docType) {
        $configPath = sprintf("sales_pdf/%s/document_title", strtolower($docType));
        return Mage::getStoreConfig($configPath);
    }

    /*    
    */
    public function getDisplayForCountries() {
        return Mage::getStoreConfig(self::XML_PATH_TAX_SALES_DISPLAY_SPECIFICCOUNTRY);
    }
 
     /**
     * showTaxMessage
     *
     * @param int billingCountryId check country in list
     * @return bool  (name)
     */
    public function showTaxMessage($billingCountryId) {
        $showTaxMessage = false;        
        //$this->log(sprintf("%s->billingCountryId=%s", __METHOD__, $billingCountryId) );
        $theseCountries = $this->getDisplayForCountries();
        //$this->log(sprintf("%s->countries=%s", __METHOD__, $theseCountries) );        
        $countries = explode(",", $theseCountries);
        //$this->log(sprintf("%s->countries=%s", __METHOD__, print_r($countries, true)) );        
        foreach($countries as $code) {
            if (preg_match(sprintf("/%s/i", $code), $billingCountryId)) {
                $showTaxMessage = true;
                break;
            }
        }
        return $showTaxMessage;
    }
    
    /**
     * showGst
     *
     * @param int billingCountryId check country in list
     * @return bool
     */
    public function showGst($billingCountryId) {
        $showGst = false;        
        //$this->log(sprintf("%s->billingCountryId=%s", __METHOD__, $billingCountryId) );
        $theseCountries = $this->getDisplayForCountries();
        //$this->log(sprintf("%s->countries=%s", __METHOD__, $theseCountries) );        
        $countries = explode(",", $theseCountries);
        //$this->log(sprintf("%s->countries=%s", __METHOD__, print_r($countries, true)) );        
        foreach($countries as $code) {
            if (preg_match(sprintf("/%s/i", $code), $billingCountryId)) {
                $showGst = true;
                break;
            }
        }
        return $showGst;
    }    

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data, $level = null)
	{
		Mage::log($data, $level, $this->logFileName);
	}    
}
