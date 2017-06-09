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
 * Abstract pdf model.
 *
 * @category  FireGento
 * @package   FireGento_Pdf
 * @author    FireGento Team <team@firegento.com>
 */
abstract class FireGento_Pdf_Model_Engine_Abstract extends Mage_Sales_Model_Order_Pdf_Abstract {

    // why aren't there different margins per paper size ???
    //public $margin = array('left' => 45, 'right' => 540);
    //public $margin = array('left' => 45, 'right' => 555);
    public $margin = array('top' => 120, 'left' => 30, 'right' => 565);
    public $colors = array();
    public $mode;
    public $encoding;
    public $pagecounter;

    protected $_imprint;
    protected $_additionalRenderers = array();

    /**
     * @var int correct all y values if the logo is full width and bigger than normal
     */
    protected $_rightHandCrnBoxY2 = 0;
    protected $_startContentY = 0;
    protected $_logoHeight = 0;

    /**
     * constructor to init settings
     */
    public function __construct()
    {
        parent::__construct();

        $this->encoding = 'UTF-8';

        $this->colors['white'] = new Zend_Pdf_Color_GrayScale(1);
        $this->colors['black'] = new Zend_Pdf_Color_GrayScale(0);
        $this->colors['grey1'] = new Zend_Pdf_Color_GrayScale(0.9);

        // get the default imprint
        $this->_imprint = Mage::getStoreConfig('general/imprint');
    }

    /**
     * Draw one line
     *
     * @param  Zend_Pdf_Page $page         Current page object of Zend_Pdf
     * @param  array         $draw         items to draw
     * @param  array         $pageSettings page settings to use for new pages
     *
     * @return Zend_Pdf_Page
     */
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array()) {

        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array'));
            }
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height'])
                            ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = count($column['text']) * $lineSpacing;

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }
            // ???
            if (
                $this->y - $itemsProp['shift'] < 50
                ||
                (Mage::getStoreConfig('sales_pdf/firegento_pdf/show_footer') == 1 && $this->y - $itemsProp['shift'] < 100)
            ) {
                $page = $this->newPage($pageSettings);
            }
            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 7
                        : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font
                            = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular'
                            : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page,
                                    $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left'
                            : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed,
                                        $width, $font, $fontSize);
                                } else {
                                    $feed = $feed
                                        - $this->widthForStringUsingFontSize($part,
                                            $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed,
                                        $width, $font, $fontSize);
                                }
                                break;
                        }
                        $page->drawText($part, $feed, $this->y - $top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }
        return $page;
    }

    /**
     * Set pdf mode.
     *
     * @param  string $mode set mode to differ between creditmemo, invoice, etc.
     *
     * @return FireGento_Pdf_Model_Engine_Abstract
     */
    public function setMode($mode) {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Return pdf mode.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set next line position
     *
     * @param  int $height Line-Height
     *
     * @return void
     */
    protected function Ln($height = 15) {
        $this->y -= $height;
        return $this->y;
    }

    /**
     * Insert sender address bar
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     *
     * @return void
     */
    protected function _insertSenderAddessBar(&$page) {
        //$config = 'sales_pdf/firegento_pdf/sender_address_bar';

        $config = 'sales/identity/address';
        if (Mage::getStoreConfig($config) != '') {
            $this->_setFontRegular($page, 8);
            $page->drawText(
                trim(Mage::getStoreConfig($config)),
                $this->margin['left'] + $this->getHeaderblockOffset(),
                $this->y, $this->encoding
            );
            $this->Ln(10);
        }
    }

    protected function _insertCompany(&$page) {
        if ($company = Mage::getStoreConfig('sales_pdf/firegento_pdf/company_name')) {
            $this->_setFontBold($page, 11);
            $page->drawText($company, $this->margin['left'] + $this->getHeaderblockOffset(), $this->y, $this->encoding);
            $this->Ln(14);
        }
    }

    protected function _insertAbn(&$page) {
        if ($abn = Mage::getStoreConfig('sales_pdf/firegento_pdf/company_abn')) {
            $this->_setFontBold($page, 10);
            $abn = sprintf("ABN: %s", $abn);
            $page->drawText($abn, $this->margin['left'] + $this->getHeaderblockOffset(), $this->y, $this->encoding);
            $this->Ln(14);
        }
    }

    /**
     * Insert logo
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     * @param  mixed         $store store to get data from
     *
     * @return void
     */
    protected function insertLogo(&$page, $store = null)
    {
        if ($this->_isLogoFullWidth($store)) {
            $this->_insertLogoFullWidth($page, $store);
        }
        else {
            $this->_insertLogoPositioned($page, $store);
        }
    }

    /**
     * is the setting to show the logo full width?
     *
     * @param  mixed $store store we want the config setting from
     *
     * @return bool
     */
    protected function _isLogoFullWidth($store)
    {
        return Mage::helper('firegento_pdf')->isLogoFullWidth($store);
    }

    /**
     * Inserts the logo if it is positioned left, center or right.
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     * @param  mixed         $store store to get data from
     *
     * @return void
     */
    protected function _insertLogoPositioned(&$page, $store = null)
    {
        $imageRatio = (int)Mage::getStoreConfig('sales_pdf/firegento_pdf/logo_ratio', $store);
        $imageRatio = (empty($imageRatio)) ? 100 : $imageRatio;

        $maxwidth = ($this->margin['right'] - $this->margin['left']) * $imageRatio / 100;
        $maxheight = 100;

        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image and file_exists(Mage::getBaseDir('media', $store) . '/sales/store/logo/' . $image)) {
            $image = Mage::getBaseDir('media', $store) . '/sales/store/logo/' . $image;
            list ($width, $height) = Mage::helper('firegento_pdf')->getScaledImageSize($image, $maxwidth, $maxheight);
            $this->_logoHeight = $height;
            if (is_file($image)) {
                $image = Zend_Pdf_Image::imageWithPath($image);
                $logoPosition = Mage::getStoreConfig('sales_pdf/firegento_pdf/logo_position', $store);
                switch ($logoPosition) {
                    case 'center':
                        $startLogoAt = $this->margin['left'] + (($this->margin['right'] - $this->margin['left']) / 2) - $width / 2;
                        break;
                    case 'right':
                        $startLogoAt = $this->margin['right'] - $width;
                        break;
                    default:
                        $startLogoAt = $this->margin['left'];
                }

                $position['x1'] = $startLogoAt;
                $position['y1'] = $this->_startContentY;
                $position['x2'] = $position['x1'] + $width;
                $position['y2'] = $position['y1'] + $this->_logoHeight;
                $this->_logoStartY1 = $this->_startContentY + $this->_logoHeight;

                $page->drawImage($image, $position['x1'], $position['y1'], $position['x2'], $position['y2']);
            }
        }
    }

    /**
     * inserts the logo from complete left to right
     *
     * @param Zend_Pdf_Page &$page current Zend_Pdf_Page object
     * @param mixed         $store store we need the config setting from
     *
     * @todo merge _insertLogoPositioned and _insertLogoFullWidth
     */
    protected function _insertLogoFullWidth(&$page, $store = null) {

        $imageRatio = (int)Mage::getStoreConfig('sales_pdf/firegento_pdf/logo_ratio', $store);
        $imageRatio = (empty($imageRatio)) ? 1 : $imageRatio;

        $maxwidth = 594 * $imageRatio / 100;
        $maxheight = 300;

        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image and file_exists(Mage::getBaseDir('media', $store) . '/sales/store/logo/' . $image)) {
            $image = Mage::getBaseDir('media', $store) . '/sales/store/logo/' . $image;
            list ($width, $height) = Mage::helper('firegento_pdf')->getScaledImageSize($image, $maxwidth, $maxheight);
            $this->_logoHeight = $height;
            if (is_file($image)) {
                $image = Zend_Pdf_Image::imageWithPath($image);

                $logoPosition = Mage::getStoreConfig('sales_pdf/firegento_pdf/logo_position', $store);

                switch ($logoPosition) {
                    case 'center':
                        $startLogoAt = $this->margin['left'] +
                            (($this->margin['right'] - $this->margin['left'])
                                / 2) - $width / 2;
                        break;
                    case 'right':
                        $startLogoAt = $this->margin['right'] - $width;
                        break;
                    default:
                        $startLogoAt = 0;
                }

                $position['x1'] = $startLogoAt;
                $position['y1'] = 663;
                $position['x2'] = $position['x1'] + $width;
                $position['y2'] = $position['y1'] + $this->_logoHeight;
                $this->_logoStartY1 = $this->_startContentY + $this->_logoHeight;

                $page->drawImage($image, $position['x1'], $position['y1'], $position['x2'], $position['y2']);
                //$this->_marginTop = $this->_logoHeight - 130;
            }
        }
    }

    /**
     * insert customer address and all header like customer number, etc.
     *
     * @param Zend_Pdf_Page             $page   current Zend_Pdf_Page
     * @param Mage_Sales_Model_Abstract $source source for the address information
     * @param Mage_Sales_Model_Order    $order  order to print the document for
     */
    protected function insertAddressesAndHeader(Zend_Pdf_Page $page, Mage_Sales_Model_Abstract $source, Mage_Sales_Model_Order $order) {

        $page->setFillColor($this->colors['black']);

        // Add logo
        $this->insertLogo($page, $source->getStore());

        // Add head
        $this->insertHeader($page, $order, $source);

        // Add company and/or abn
        $this->y = $this->_logoStartY1 - ($this->_logoHeight + 15);
        $this->_insertCompany($page);
        $this->_insertAbn($page);

        // Add sender address
        // sales_identity_address
        // general_store_information_address
        $this->_insertSenderAddessBar($page);

        if ($this->_rightHandCrnBoxY2 < $this->y) {
            $this->y = $this->_rightHandCrnBoxY2;
        }
        $this->Ln();
        //Mage::helper('firegento_pdf')->log(sprintf("%s->_rightHandCrnBoxY2=%s|%s", __METHOD__, $this->_rightHandCrnBoxY2, $this->y) );

        // draw address box
        $drawAddressBox = false;
        $addressesHeight = 0;

        if ($order->getBillingAddress()) {
            $address = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
            $h1 = $this->_calcAddressHeight($address);
            $addressesHeight = $h1;
            $drawAddressBox = true;
        }

        if ($order->getShippingAddress()) {
            $address = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            $h1 = $this->_calcAddressHeight($address);
            $drawAddressBox = true;
            if ($h1 > $addressesHeight) {
                $addressesHeight = $h1;
            }
        }

        // was an address title printed? e.g. ship to etc
        $showTitles = $this->_showAddressTitles($order->getStore());
        if ($showTitles) {
            $addressesHeight += 15;
        }
        //Mage::helper('firegento_pdf')->log(sprintf("%s->height=%d,y=%s|%s", __METHOD__, $addressesHeight, $this->y, ($this->y - $addressesHeight)) );
        if ($drawAddressBox) {
            $startY = $this->y;
            $page->drawRectangle($this->margin['left'], $this->y, $this->margin['right'], ($this->y - $addressesHeight), Zend_Pdf_Page::SHAPE_DRAW_STROKE);

            $addressY = $this->Ln();

            // Add billing address
            if ($order->getBillingAddress()) {
                $prevM = $this->margin;
                $this->margin = array('left' => $this->margin['left'] + 5, 'right' => $this->margin['right'] - 5);
                $this->_insertCustomerAddress($page, $order, false, $showTitles);
                $this->margin = $prevM;
            }

            // Add shipping address
            if ($order->getShippingAddress()) {
                $prevM = $this->margin;
                $this->margin = array('left' => 305, 'right' => $this->margin['right'] - 5);
                $this->y = $addressY;
                $this->_insertCustomerAddress($page, $order, true, $showTitles);
                $this->margin = $prevM;
            }

            //Mage::helper('firegento_pdf')->log(sprintf("%s->this->y=%s", __METHOD__, $this->y) );
            $this->y = $startY - $addressesHeight;

            $this->Ln();
            $this->Ln();
        }
        //Mage::helper('firegento_pdf')->log(sprintf("%s->this->y=%s", __METHOD__, $this->y) );

        // document type title + items
        $this->_setFontBold($page, 15);
        $title = $this->getTitle();
        $page->drawText(Mage::helper('firegento_pdf')->__($title), $this->margin['left'], $this->y, $this->encoding);
        $this->Ln();
    }

    /**
     * Inserts the customer's shipping or billing address.
     *
     * @param  Zend_Pdf_Page          &$page current page object of Zend_Pdf
     * @param  Mage_Sales_Model_Order $order order object
     *
     * @return void
     */
    //protected function _insertCustomerAddress(&$page, $order)
    /*
    protected function _insertCustomerAddress(&$page, $order, $shipping = false, $showTitles = false) {
        $this->_setFontRegular($page, 9);
        $billing = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
        //$shipping = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
        foreach ($billing as $line) {
            $text = trim(strip_tags($line));
            if (!empty($text)) {
                $page->drawText($text,
                    $this->margin['left'] + $this->getHeaderblockOffset(), $this->y,
                    $this->encoding
                );
            }
            $this->Ln(12);
        }
    }
    */
    protected function _insertCustomerAddress(&$page, $order, $shippingAddress = false, $showTitles = false) {
        // show sold to/ship to
        if ($showTitles) {
            $this->_setFontBold($page, 12);
            $page->drawText(($shippingAddress ? Mage::helper('sales')->__('Ship to:') : Mage::helper('sales')->__('Sold to:') ),
                $this->margin['left'] + $this->getHeaderblockOffset(), $this->y,
                $this->encoding);
            $this->Ln(14);
        }
        $this->_setFontRegular($page, 9);

        if ($shippingAddress && $order->getShippingAddress()) {
            $address = $order->getShippingAddress()->format('pdf');
        }
        else if ($order->getBillingAddress()) {
            $address = $order->getBillingAddress()->format('pdf');
        }
        else {
            $address = false;
        }
        if ($address) {
            //Mage::helper('firegento_pdf')->log(sprintf("%s->address=%s", __METHOD__, $address) );
            $formatAddress = $this->_formatAddress($address);
            //Mage::helper('firegento_pdf')->log(sprintf("%s->formatAddress=%s", __METHOD__, print_r($formatAddress, true)) );

            $cnt = 0;
            foreach ($formatAddress as $line) {
                $cnt++;
                $this->_setFontRegular($page, 9);
                $text = trim(strip_tags($line));
                // bold first line
                if ($cnt == 1) {
                    $this->_setFontBold($page, 10);
                }
                $page->drawText($text,
                    $this->margin['left'] + $this->getHeaderblockOffset(), $this->y,
                    $this->encoding);
                $this->Ln(12);
            }
        }
    }

    /**
     * get the offset to position the address block left or right
     *
     * @return int
     */
    protected function getHeaderblockOffset()
    {
        if (Mage::getStoreConfig('sales_pdf/firegento_pdf/headerblocks_position')
            == FireGento_Pdf_Model_System_Config_Source_Headerblocks::LEFT
        ) {
            $offsetAdjustment = 0;
        } else {
            $offsetAdjustment = 315;
        }

        return $offsetAdjustment;
    }

    /**
     * get title
     * checks mode: Invoice/shipment/creditmemo Number
     * @return string title
     */
    protected function getTitle($numberTitle = false) {
        $mode = $this->getMode();

        if (Mage::helper('firegento_pdf')->getDocumentTitle($mode)) {
            $title = Mage::helper('firegento_pdf')->getDocumentTitle($mode);
        }
        else {
            if ($mode == 'order' && $numberTitle) {
                $title = 'Order';
            }
            elseif ($mode == 'order') {
                $title = 'Invoice';
            }
            elseif ($mode == 'invoice') {
                $title = 'Invoice';
            }
            elseif ($mode == 'shipment') {
                $title = 'Shipment';
            }
            else {
                $title = 'Creditmemo';
            }
        }
        if ($numberTitle) {
            $title = sprintf("%s number:", $title);
        }
        return $title;
    }

    /**
     * Insert Header
     *
     * @param  Zend_Pdf_Page          &$page    Current page object of Zend_Pdf
     * @param  Mage_Sales_Model_Order $order    Order object
     * @param  object                 $document Document object
     *
     * @return void
     */
    protected function insertHeader(&$page, $order, $document) {

        $page->setFillColor($this->colors['black']);
        $this->_setFontBold($page, 15);

        // document type title + items
        //$page->drawText(Mage::helper('firegento_pdf')->__($title + ' items'), $this->margin['left'], $this->y, $this->encoding);

        if (is_null($this->_logoStartY1) || empty( $this->_logoStartY1)) {
            $this->_logoStartY1 = $this->_startContentY;
        }
        //Mage::helper('firegento_pdf')->log(sprintf("%s->_logoStartY1=%s", __METHOD__, $this->_logoStartY1) );
        $this->y = $this->_logoStartY1;
        $this->y -= 20;

        //$labelRightOffset = 180 + $this->getHeaderblockOffset();
        $labelRightOffset = 70 + $this->getHeaderblockOffset();
        //$valueRightOffset = 10 + $this->getHeaderblockOffset();
        $valueRightOffset = 65 + $this->getHeaderblockOffset();

        $width = 80;
        $numberOfLines = 0;

        // document type title
        if (true) {
            $titleFont = $this->_setFontBold($page, 15);
            $page->drawText(
                Mage::helper('firegento_pdf')->__($this->getTitle()),
                //($this->margin['right'] - $labelRightOffset),
                ($this->margin['right'] - $labelRightOffset - $this->widthForStringUsingFontSize($this->getTitle(), $titleFont, 15)),
                $this->y, $this->encoding
            );
            $this->Ln();
            $numberOfLines++;
        }

        $font = $this->_setFontRegular($page, 10);

        $labelText = $this->getTitle(true);
        $page->drawText(
            Mage::helper('firegento_pdf')->__($labelText),
            //($this->margin['right'] - $labelRightOffset),
            ($this->margin['right'] - $labelRightOffset - $this->widthForStringUsingFontSize($labelText, $font, 10)),
            $this->y,
            $this->encoding
        );

        $incrementId = $document->getIncrementId();
        $page->drawText(
            $incrementId,
            //($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($incrementId, $font, 10)),
            ($this->margin['right'] - $valueRightOffset),
            $this->y,
            $this->encoding
        );
        $this->Ln();
        $numberOfLines++;

        // Order Number
        $putOrderId = $this->_putOrderId($order);
        if ($putOrderId) {
            $labelText = Mage::helper('firegento_pdf')->__('Order number:');
            $page->drawText(
                $labelText,
                //($this->margin['right'] - $labelRightOffset),
                ($this->margin['right'] - $labelRightOffset - $this->widthForStringUsingFontSize($labelText, $font, 10)),
                $this->y, $this->encoding
            );
            $page->drawText(
                $putOrderId,
                //($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($putOrderId, $font, 10)),
                ($this->margin['right'] - $valueRightOffset),
                $this->y, $this->encoding
            );
            $this->Ln();
            $numberOfLines++;
        }

        // Customer Number
        if ($this->_showCustomerNumber($order->getStore())) {
            $labelText = Mage::helper('firegento_pdf')->__('Customer number:');
            $page->drawText(
                $labelText,
                //($this->margin['right'] - $labelRightOffset),
                ($this->margin['right'] - $labelRightOffset - $this->widthForStringUsingFontSize($labelText, $font, 10)),
                $this->y, $this->encoding
            );
            $customerid = '-';
            if ($order->getCustomerId() != '') {
                $prefix = Mage::getStoreConfig('sales_pdf/invoice/customeridprefix');
                if (!empty($prefix)) {
                    $customerid = $prefix . $order->getCustomerId();
                }
                else {
                    $customerid = $order->getCustomerId();
                }
            }
            $page->drawText(
                $customerid,
                //($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($customerid, $font, 10)),
                ($this->margin['right'] - $valueRightOffset),
                $this->y, $this->encoding
            );
            $this->Ln();
            $numberOfLines++;
        }

        /** print VAT ID */
        if ($this->_showCustomerVATNumber($order->getStore())) {
            $labelText = Mage::helper('firegento_pdf')->__('VAT-ID:');
            $page->drawText(
                $labelText,
                //($this->margin['right'] - $labelRightOffset),
                ($this->margin['right'] - $labelRightOffset - $this->widthForStringUsingFontSize($labelText, $font, 10)),
                $this->y, $this->encoding
            );
            $customerVatId = ($order->getCustomerTaxvat()) ? $order->getCustomerTaxvat() : '-';
            $font = $this->_setFontRegular($page, 10);
            $page->drawText(
                $customerVatId,
                //($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($customerVatId, $font, 10)),
                ($this->margin['right'] - $valueRightOffset),
                $this->y, $this->encoding
            );
            $this->Ln();
            $numberOfLines++;
        }
        /** end VAT ID print*/

        // Customer IP
        /*
        if (!Mage::getStoreConfigFlag('sales/general/hide_customer_ip', $order->getStoreId())) {
            $page->drawText(
                Mage::helper('firegento_pdf')->__('Customer IP:'),
                ($this->margin['right'] - $labelRightOffset),
                $this->y, $this->encoding
            );
            $customerIP = $order->getData('remote_ip');
            $font = $this->_setFontRegular($page, 10);
            $page->drawText(
                $customerIP, ($this->margin['right'] - $valueRightOffset
                - $this->widthForStringUsingFontSize(
                    $customerIP, $font, 10
                )), $this->y, $this->encoding
            );
            $this->Ln();
            $numberOfLines++;
        }
        */
        $labelText = Mage::helper('firegento_pdf')->__(($this->getMode() == 'invoice' || $this->getMode() == 'order') ? 'Invoice date:' : 'Date:');
        $page->drawText(
            $labelText,
            //($this->margin['right'] - $labelRightOffset),
            ($this->margin['right'] - $labelRightOffset - $this->widthForStringUsingFontSize($labelText, $font, 10)),
            $this->y, $this->encoding
        );
        $documentDate = Mage::helper('core')->formatDate($document->getCreatedAtDate(), 'medium', false);
        $page->drawText(
            $documentDate,
            //($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($documentDate, $font, 10)),
            ($this->margin['right'] - $valueRightOffset),
            $this->y, $this->encoding
        );
        $this->Ln();
        $numberOfLines++;

        // Payment method.
        //$putPaymentMethod = ($this->getMode() == 'invoice' && Mage::getStoreConfig('sales_pdf/invoice/payment_method_position') == FireGento_Pdf_Model_System_Config_Source_Payment::POSITION_HEADER);
        $putPaymentMethod = (Mage::getStoreConfig('sales_pdf/invoice/payment_method_position') == FireGento_Pdf_Model_System_Config_Source_Payment::POSITION_HEADER);
        if ($putPaymentMethod) {
            $labelText = Mage::helper('firegento_pdf')->__('Payment method:');
            $page->drawText(
                $labelText,
                ($this->margin['right'] - $labelRightOffset - $this->widthForStringUsingFontSize($labelText, $font, 10)),
                //($this->margin['right'] - $labelRightOffset),
                $this->y, $this->encoding
            );
            $paymentMethodArray = $this->_prepareText(
                $order->getPayment()->getMethodInstance()->getTitle(), $page,
                $font, 10, $width
            );
            $paymentMethod = array_shift($paymentMethodArray);
            $page->drawText(
                $paymentMethod,
                //($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($paymentMethod, $font, 10)),
                ($this->margin['right'] - $valueRightOffset),
                $this->y, $this->encoding
            );
            $this->Ln();
            $numberOfLines++;
            $paymentMethodArray = $this->_prepareText(implode(" ", $paymentMethodArray), $page, $font, 10, 2 * $width);
            foreach ($paymentMethodArray as $methodString) {
                $page->drawText(
                    $methodString,
                    //$this->margin['right'] - $labelRightOffset, $this->y,
                    ($this->margin['right'] - $labelRightOffset - $this->widthForStringUsingFontSize($methodString, $font, 10)),
                    $this->encoding);
                $this->Ln();
                $numberOfLines++;
            }

        }

        // Shipping method.
        $putShippingMethod = (
            $this->getMode() == 'order'
            &&
            Mage::getStoreConfig('sales_pdf/invoice/shipping_method_position') == FireGento_Pdf_Model_System_Config_Source_Shipping::POSITION_HEADER
            ||
            $this->getMode() == 'invoice'
            &&
            Mage::getStoreConfig('sales_pdf/invoice/shipping_method_position') == FireGento_Pdf_Model_System_Config_Source_Shipping::POSITION_HEADER
            ||
            $this->getMode() == 'shipment'
            &&
            Mage::getStoreConfig('sales_pdf/shipment/shipping_method_position') == FireGento_Pdf_Model_System_Config_Source_Shipping::POSITION_HEADER);

        if ($putShippingMethod) {
            $page->drawText(
                Mage::helper('firegento_pdf')->__('Shipping method:'),
                ($this->margin['right'] - $labelRightOffset),
                $this->y, $this->encoding
            );
            $shippingMethodArray = $this->_prepareText($order->getShippingDescription(), $page, $font, 10, $width);
            $shippingMethod = array_shift($shippingMethodArray);
            $page->drawText(
                $shippingMethod,
                //($this->margin['right'] - $valueRightOffset - $this->widthForStringUsingFontSize($shippingMethod, $font, 10)),
                ($this->margin['right'] - $valueRightOffset),
                $this->y, $this->encoding
            );
            $this->Ln();
            $numberOfLines++;
            $shippingMethodArray = $this->_prepareText(
                implode(" ", $shippingMethodArray), $page, $font, 10, 2 * $width
            );
            foreach ($shippingMethodArray as $methodString) {
                $page->drawText($methodString,
                    $this->margin['right'] - $labelRightOffset, $this->y,
                    $this->encoding);
                $this->Ln();
                $numberOfLines++;
            }
        }

        // top right hand corner: title + details
        $this->_rightHandCrnBoxY2 = $this->_logoStartY1 - (($numberOfLines * 15) + 20);
        //Mage::helper('firegento_pdf')->log(sprintf("%s->lines=%d,y1=%s,y2=%s", __METHOD__, $numberOfLines, ($this->_logoStartY1 + 20), $this->_rightHandCrnBoxY2) );

        $page->drawRectangle(
            $x1 = 365,
            $this->_logoStartY1,
            $this->margin['right'],
            $this->_rightHandCrnBoxY2,
            Zend_Pdf_Page::SHAPE_DRAW_STROKE
        );

        //$this->y -= ($numberOfLines * 2);
        $this->y = $this->_rightHandCrnBoxY2;
        $this->Ln();
    }

    /**
     * Return the order id or false if order id should not be displayed on document.
     *
     * @param  Mage_Sales_Model_Order $order order to get id from
     *
     * @return int|false
     */
    protected function _putOrderId($order)
    {
        return Mage::helper('firegento_pdf')->putOrderId($order, $this->mode);
    }

    /**
     * do we show the customber number on this document
     *
     * @param  mixed $store store from whom we need the config setting
     *
     * @return bool
     */
    protected function _showCustomerNumber($store)
    {
        return Mage::helper('firegento_pdf')->showCustomerNumber($this->mode, $store);
    }

    /**
     * do we show the address titles ship to/from
     *
     * @param  mixed $store store from whom we need the config setting
     *
     * @return bool
     */
    protected function _showAddressTitles($store)
    {
        return Mage::helper('firegento_pdf')->showAddressTitles($this->mode, $store);
    }

    /**
     * do we show the customber VAT number on this document
     *
     * @param  mixed $store store from whom we need the config setting
     *
     * @return bool
     */
    protected function _showCustomerVATNumber($store)
    {
        return Mage::helper('firegento_pdf')
            ->showCustomerVATNumber($this->mode, $store);
    }

    /**
     * Generate new PDF page.
     *
     * @param  array $settings Page settings
     *
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array()) {

        $pdf = $this->_getPdf();
        // e.g. 595:842:
        $sizes = explode(":", $this->getPageSize());
        //Mage::helper('firegento_pdf')->log(sprintf("%s->PageSize: %s", __METHOD__, print_r($sizes, true)) );

        $page = $pdf->newPage($this->getPageSize());
        $this->pagecounter++;
        $pdf->pages[] = $page;

        $this->_addFooter($page, Mage::app()->getStore());

        // set the font because it may not be set
        // see https://github.com/firegento/firegento-pdf/issues/184
        $this->_setFontRegular($page, 9);

        // provide the possibility to add random stuff to the page
        Mage::dispatchEvent(
            'firegento_pdf_' . $this->getMode() . '_edit_page',
            array(
                'page'  => $page,
                'order' => $this->getOrder()
            )
        );

        /*
        start at the top margin

        seems to only be defined for A4 595:842:
        */
        if (is_array($sizes) && count($sizes) >= 2 && $sizes[1] > 800) {
            $this->_startContentY = $sizes[1] - $this->margin['top'];
        }
        else {
            $this->_startContentY = $sizes[1] - 80; // top margin ...
        }
        $this->y = $this->_startContentY;

        // set font back to org in case observer did not
        $this->_setFontRegular($page, 9);

        return $page;
    }

    /**
     * Draw
     *
     * @param  Varien_Object          $item     creditmemo/shipping/invoice to draw
     * @param  Zend_Pdf_Page          $page     Current page object of Zend_Pdf
     * @param  Mage_Sales_Model_Order $order    order to get infos from
     * @param  int                    $position position in table
     *
     * @return Zend_Pdf_Page
     */
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order, $position = 1) {

        // returns configurable | bundle etc
        $type = $item->getOrderItem()->getProductType();

        //Mage::helper('firegento_pdf')->log(sprintf("%s->statusId: %s", __METHOD__, $item->getStatusId()) );

        // use
        if ($item->getStatusId() == Mage_Sales_Model_Order_Item::STATUS_PENDING) {
            $type = "order";
            if (is_null($this->_additionalRenderers[$type]['renderer'])) {
                $model = "firegento_pdf/items_order_default";
                $this->_additionalRenderers[$type]['renderer'] = Mage::getSingleton($model);
            }
            // FireGento_Pdf_Model_Order_Pdf_Items_Order_Default
            $renderer = $this->_additionalRenderers[$type]['renderer'];
        }
        else {
            /*
            for available rendered see Mage_Sales_Model_Order_Pdf_Abstract::_initRenderer

            sales/order_pdf_items_invoice_default
            sales/order_pdf_items_invoice_grouped
            bundle/sales_order_pdf_items_invoice
            downloadable/sales_order_pdf_items_invoice

            */
            $renderer = $this->_getRenderer($type);
        }

        // TODO: create a new renderer for Unirgy_Giftcert_Model_Pdf_Item
        if (get_class($renderer) == 'Unirgy_Giftcert_Model_Pdf_Item') {
            Mage::helper('firegento_pdf')->log(sprintf("%s->renderer: %s", __METHOD__, get_class($renderer)) );
            $renderer = new FireGento_Pdf_Model_Items_Order_Default();
        }

        Mage::helper('firegento_pdf')->log(sprintf("%s->renderer: %s", __METHOD__, get_class($renderer)) );

        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw($position);

        return $renderer->getPage();
    }

    /**
     * Insert Totals Block
     *
     * @param  object $page   Current page object of Zend_Pdf
     * @param  object $source Fields of footer
     *
     * @return Zend_Pdf_Page
     */
    protected function insertTotals($page, $source) {
        $this->y -= 15;

        $order = $source->getOrder();

        $totalTax = 0;
        $shippingTaxRate = 0;
        $shippingTaxAmount = $order->getShippingTaxAmount();

        if ($shippingTaxAmount > 0) {
            $shippingTaxRate = $order->getShippingTaxAmount() * 100 / ($order->getShippingInclTax() - $order->getShippingTaxAmount());
        }

        $groupedTax = array();

        $items['items'] = array();
        foreach ($source->getAllItems() as $item) {
            //Mage::helper('firegento_pdf')->log(sprintf("%s->item: %s", __METHOD__, get_class($item)));
            if (get_class($item) == 'Mage_Sales_Model_Order_Item') {
                if ($item->getParentItemId()) {
                    continue;
                }
                $items['items'][] = $item->toArray();
            }
            else {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                $items['items'][] = $item->getOrderItem()->toArray();
            }
        }

        array_push(
            $items['items'], array(
                'row_invoiced'     => $order->getShippingInvoiced(),
                'tax_inc_subtotal' => false,
                'tax_percent'      => $shippingTaxRate,
                'tax_amount'       => $shippingTaxAmount
            )
        );

        foreach($items['items'] as $item) {
            $_percent = null;
            if (!isset($item['tax_amount'])) {
                $item['tax_amount'] = 0;
            }
            if (!isset($item['row_invoiced'])) {
                $item['row_invoiced'] = 0;
            }
            if (!isset($item['price'])) {
                $item['price'] = 0;
            }
            if (!isset($item['tax_inc_subtotal'])) {
                $item['tax_inc_subtotal'] = 0;
            }
            if (((float)$item['tax_amount'] > 0)
                && ((float)$item['row_invoiced'] > 0)
            ) {
                $_percent = round($item["tax_percent"], 0);
            }
            if (!array_key_exists('tax_inc_subtotal', $item)
                || $item['tax_inc_subtotal']
            ) {
                $totalTax += $item['tax_amount'];
            }
            if (($item['tax_amount']) && $_percent) {
                if (!array_key_exists((int)$_percent, $groupedTax)) {
                    $groupedTax[$_percent] = $item['tax_amount'];
                } else {
                    $groupedTax[$_percent] += $item['tax_amount'];
                }
            }
        }

        /*
        for a list of totals see
        <global><pdf><totals>
        */
        $totals = $this->_getTotalsList($source);
        //Mage::helper('firegento_pdf')->log(sprintf("%s->totals=%s", __METHOD__, print_r($totals, true)) );

        $lineBlock = array(
            'lines'  => array(),
            'height' => 20
        );

        foreach ($totals as $total) {
            //Mage::helper('firegento_pdf')->log(sprintf("%s->total=%s", __METHOD__, print_r($total, true)) );
            //Mage::helper('firegento_pdf')->log(sprintf("%s->total=%s", __METHOD__, $total->getTitle()) );
            $total->setOrder($order)->setSource($source);
            if ($total->canDisplay()) {
                $total->setFontSize(10);
                // fix Magento 1.8 bug, so that taxes for shipping do not appear twice
                // see https://github.com/firegento/firegento-pdf/issues/106
                $totals = $total->getTotalsForDisplay();
                $uniqueTotalsForDisplay = array_map(
                    'unserialize',
                    array_unique(
                        array_map(
                            'serialize',
                            $totals
                        )
                    )
                );
                //Mage::helper('firegento_pdf')->log(sprintf("%s->uniqueTotalsForDisplay=%s", __METHOD__, print_r($uniqueTotalsForDisplay, true)) );
                //Mage::helper('firegento_pdf')->log(sprintf("%s->uniqueTotalsForDisplay[0]['label']=%s", __METHOD__, $uniqueTotalsForDisplay[0]['label']) );

                foreach ($uniqueTotalsForDisplay as $totalData) {
                    $label = $this->fixNumberFormat($totalData['label']);
                    // fix Discount () <-- empty?
                    if (preg_match("/discount/i", $label)) {
                        //Mage::helper('firegento_pdf')->log(sprintf("%s->getDiscountDescription=%s", __METHOD__, $order->getDiscountDescription()) );
                        $label = sprintf("Discount (%s):", $order->getDiscountDescription());
                    }
                    /*
                    if (Mage::getStoreConfig('sales_pdf/invoice/show_item_discount') && preg_match("/subtotal/i", $label)) {
                        //Mage::helper('firegento_pdf')->log(sprintf("%s->getDiscountDescription=%s", __METHOD__, $order->getDiscountDescription()) );
                        $label = "Subtotal (ex. discount):";
                    }
                    */
                    //Mage::helper('firegento_pdf')->log(sprintf("%s->%s,label=%s", __METHOD__, get_class($total), $label) );
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $label,
                            'feed'      => 470,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size']
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => $this->margin['right'],
                            'align'     => 'right',
                            'font_size' => $totalData['font_size']
                        ),
                    );
                }
            }
        }
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }

    /**
     * Insert Notes
     *
     * @param  Zend_Pdf_Page             $page   Current Page Object of Zend_PDF
     * @param  Mage_Sales_Model_Order    &$order order to get note from
     * @param  Mage_Sales_Model_Abstract &$model invoice/shipment/creditmemo
     *
     * @return \Zend_Pdf_Page
     */
    protected function _insertNote($page, &$order, &$model) {

        $notes = array();
        $result = new Varien_Object();
        $result->setNotes($notes);
        Mage::dispatchEvent(
            'firegento_pdf_' . $this->getMode() . '_insert_note',
            array(
                'order'          => $order,
                $this->getMode() => $model,
                'result'         => $result
            )
        );
        $notes = array_merge($notes, $result->getNotes());

        // get free text notes
        $note = Mage::getStoreConfig('sales_pdf/' . $this->getMode() . '/note');
        if (!empty($note)) {
            $tmpNotes = explode("\n", $note);
            $notes = array_merge($notes, $tmpNotes);
        }

        // notes header
        //Mage::helper('firegento_pdf')->log(sprintf("%s->notes=%s", __METHOD__, count($notes)) );
        if (count($notes)) {
            $allNotes = trim(implode("", $notes));
            //Mage::helper('firegento_pdf')->log(sprintf("%s->allNotes=%s|%d", __METHOD__, $allNotes, empty($allNotes)) );
            if (!empty($allNotes)) {
                $y = $this->Ln(15 * 4);
                // create a new page if necessary (this logic should be in LN function)
                if ($this->y < 50 || (Mage::getStoreConfig('sales_pdf/firegento_pdf/show_footer') == 1 && $this->y < 100)) {
                    $page = $this->newPage(array());
                }
                $this->_setFontBold($page, 11);
                $page->drawText("Notes", $this->margin['left'], $this->y, $this->encoding);
                $this->Ln();
            }
        }
        $fontSize = 10;
        $font = $this->_setFontRegular($page, $fontSize);

        // print notes
        foreach ($notes as $note) {
            // prepare the text so that it fits to the paper
            foreach ($this->_prepareText($note, $page, $font, 10) as $tmpNote) {
                // create a new page if necessary (this logic should be in LN function)
                if ($this->y < 50 || (Mage::getStoreConfig('sales_pdf/firegento_pdf/show_footer') == 1 && $this->y < 100)) {
                    $page = $this->newPage(array());
                    $font = $this->_setFontRegular($page, $fontSize);
                }
                $page->drawText($tmpNote, $this->margin['left'], $this->y, $this->encoding);
                $this->Ln(15);
            }
        }
        return $page;
    }

    /**
     * draw footer on pdf
     *
     * @param Zend_Pdf_Page &$page page to draw on
     * @param mixed         $store store to get infos from
     */
    protected function _addFooter(&$page, $store = null) {

        if (Mage::getStoreConfig('sales_pdf/firegento_pdf/show_footer') == 1) {
            $this->y = 90;
            $this->_insertFooter($page);
        }
        if (Mage::getStoreConfig('sales_pdf/firegento_pdf/show_page_nbr') == 1) {
            // Add page counter.
            $this->y = 90;
            $this->_insertPageCounter($page);
        }
    }

    /**
     * Insert footer
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     *
     * @return void
     */
    protected function _insertFooter(&$page)
    {
        $page->setLineColor($this->colors['black']);
        $page->setLineWidth(0.5);
        $page->drawLine($this->margin['left'], $this->y - 5, $this->margin['right'], $this->y - 5);

        $this->Ln(15);

        $this->_insertThankyouMessage($page);

        $this->_insertFooterAddress($page);

        $fields = array(
            'telephone' => Mage::helper('firegento_pdf')->__('Telephone:'),
            'fax'       => Mage::helper('firegento_pdf')->__('Fax:'),
            'email'     => Mage::helper('firegento_pdf')->__('E-Mail:'),
            'web'       => Mage::helper('firegento_pdf')->__('Web:')
        );
        $this->_insertFooterBlock($page, $fields, 70, 40, 140);

        $fields = array(
            'bank_name'          => Mage::helper('firegento_pdf')->__('Bank name:'),
            'bank_account'       => Mage::helper('firegento_pdf')->__('Account:'),
            'bank_code_number'   => Mage::helper('firegento_pdf')->__('Bank number:'),
            'bank_account_owner' => Mage::helper('firegento_pdf')->__('Account owner:'),
            'swift'              => Mage::helper('firegento_pdf')->__('SWIFT:'),
            'iban'               => Mage::helper('firegento_pdf')->__('IBAN:')
        );
        $this->_insertFooterBlock($page, $fields, 215, 50, 140);

        $fields = array(
            'tax_number'      => Mage::helper('firegento_pdf')->__('Tax number:'),
            'vat_id'          => Mage::helper('firegento_pdf')->__('VAT-ID:'),
            'register_number' => Mage::helper('firegento_pdf')->__('Register number:'),
            'ceo'             => Mage::helper('firegento_pdf')->__('CEO:'),
            'city'            => Mage::helper('firegento_pdf')->__('Registered seat:'),
            'court'           => Mage::helper('firegento_pdf')->__('Register court:'),
        );
        $this->_insertFooterBlock($page, $fields, 355, 60, $this->margin['right'] - 365 - 10);
    }

    /**
     * Insert footer block
     *
     * @param  Zend_Pdf_Page &$page       Current page object of Zend_Pdf
     * @param  array         $fields      Fields of footer
     * @param  int           $colposition Starting colposition
     * @param  int           $valadjust   Margin between label and value
     * @param  int           $colwidth    the width of this footer block - text will be wrapped if it is broader
     *                                    than this width
     *
     * @return void
     */
    protected function _insertFooterBlock(&$page, $fields, $colposition = 0, $valadjust = 30, $colwidth = null) {

        $fontSize = 7;
        $font = $this->_setFontRegular($page, $fontSize);
        $y = $this->y;

        $valposition = $colposition + $valadjust;

        if (is_array($fields)) {
            foreach ($fields as $field => $label) {
                if (empty($this->_imprint[$field])) {
                    continue;
                }
                // draw the label
                $page->drawText($label, $this->margin['left'] + $colposition, $y, $this->encoding);
                // prepare the value: wrap it if necessary
                $val = $this->_imprint[$field];
                $width = $colwidth;
                if (!empty($colwidth)) {
                    // calculate the maximum width for the value
                    $width = $this->margin['left'] + $colposition + $colwidth - ($this->margin['left'] + $valposition);
                }
                foreach ($this->_prepareText($val, $page, $font, $fontSize, $width) as $tmpVal) {
                    $page->drawText($tmpVal,
                        $this->margin['left'] + $valposition, $y,
                        $this->encoding);
                    $y -= 12;
                }
            }
        }
    }


    /**
     * Function name
     *
     * what the function does
     *
     * @param (type) (name) about this param
     * @return (type) (name)
     */
    protected function _insertThankyouMessage(&$page, $store = null) {
        $fontSize = 10;
        $font = $this->_setFontItalic($page, $fontSize);
        $y = $this->Ln();

        $thankyou = Mage::getStoreConfig('sales_pdf/firegento_pdf/thankyou_message');
        if (!empty($thankyou)) {
            $thankyou = trim(strip_tags($thankyou));
            $page->drawText(
                $thankyou,
                (($this->margin['right'] - $this->widthForStringUsingFontSize($thankyou, $font, $fontSize)) / 2),
                $this->y,
                $this->encoding
            );
            $this->Ln();
        }
    }

    /**
     * Insert address of store owner
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     * @param  mixed         $store store to get info from
     *
     * @return void
     */
    protected function _insertFooterAddress(&$page, $store = null) {
        $fontSize = 7;
        $font = $this->_setFontRegular($page, $fontSize);
        $y = $this->y;
        $address = '';

        foreach ($this->_prepareText($this->_imprint['company_first'], $page, $font, $fontSize, 90) as $companyFirst) {
            $address .= $companyFirst . "\n";
        }
        if ($this->_imprint && array_key_exists('company_second', $this->_imprint)) {
            foreach ($this->_prepareText($this->_imprint['company_second'], $page, $font, $fontSize, 90) as $companySecond) {
                $address .= $companySecond . "\n";
            }
        }

        if ($this->_imprint && array_key_exists('street', $this->_imprint)) {
            $address .= $this->_imprint['street'] . "\n";
        }
        if ($this->_imprint && array_key_exists('zip', $this->_imprint)) {
            $address .= $this->_imprint['zip'] . " ";
        }
        if ($this->_imprint && array_key_exists('city', $this->_imprint)) {
            $address .= $this->_imprint['city'] . "\n";
        }

        if ($this->_imprint && !empty($this->_imprint['country'])) {
            $countryName = Mage::getModel('directory/country')
                ->loadByCode($this->_imprint['country'])->getName();
            $address .= Mage::helper('core')->__($countryName);
        }

        foreach (explode("\n", $address) as $value) {
            if ($value !== '') {
                $page->drawText(trim(strip_tags($value)),
                    $this->margin['left'] - 20, $y, $this->encoding);
                $y -= 12;
            }
        }
    }

    /**
     * Insert page counter
     *
     * @param  Zend_Pdf_Page &$page Current page object of Zend_Pdf
     *
     * @return void
     */
    protected function _insertPageCounter(&$page) {
        $font = $this->_setFontRegular($page, 9);
        $text = Mage::helper('firegento_pdf')->__('Page') . ' ' . $this->pagecounter;
        $page->drawText(
            $text,
            $this->margin['right'] - $this->widthForStringUsingFontSize($text, $font, 9),
            $this->y,
            $this->encoding
        );
    }

    /**
     * get stanard font
     *
     * @return Zend_Pdf_Resource_Font the regular font
     */
    public function getFontRegular() {
        if ($this->getRegularFont() && $this->regularFontFileExists()) {
            return Zend_Pdf_Font::fontWithPath($this->getRegularFontFile());
        }

        return Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
    }

    /**
     * Set default font
     *
     * @param  Zend_Pdf_Page $object Current page object of Zend_Pdf
     * @param  string|int    $size   Font size
     *
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 10) {
        $font = $this->getFontRegular();
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * get default bold font
     *
     * @return Zend_Pdf_Resource_Font the bold font
     */
    public function getFontBold() {
        if ($this->getBoldFont() && $this->boldFontFileExists()) {
            return Zend_Pdf_Font::fontWithPath($this->getBoldFontFile());
        }
        return Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
    }

    /**
     * Set bold font
     *
     * @param  Zend_Pdf_Page $object Current page object of Zend_Pdf
     * @param  string|int    $size   Font size
     *
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 10) {
        $font = $this->getFontBold();
        $object->setFont($font, $size);

        return $font;
    }

    /**
     * get italic font
     *
     * @return Zend_Pdf_Resource_Font
     */
    public function getFontItalic() {
        if ($this->getItalicFont() && $this->italicFontFileExists()) {
            return Zend_Pdf_Font::fontWithPath($this->getItalicFontFile());
        }
        return Zend_Pdf_Font::fontWithName(
            Zend_Pdf_Font::FONT_HELVETICA_ITALIC
        );
    }

    /**
     * Set italic font
     *
     * @param  Zend_Pdf_Page $object Current page object of Zend_Pdf
     * @param  string|int    $size   Font size
     *
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 10) {
        $font = $this->getFontItalic();
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Prepares the text so that it fits to the given page's width.
     *
     * @param  string                 $text     the text which should be prepared
     * @param  Zend_Pdf_Page          $page     the page on which the text will be rendered
     * @param  Zend_Pdf_Resource_Font $font     the font with which the text will be rendered
     * @param  int                    $fontSize the font size with which the text will be rendered
     * @param  int                    $width    [optional] the width for the given text, defaults to the page width
     *
     * @return array the given text in an array where each item represents a new line
     */
    public function _prepareText($text, $page, $font, $fontSize, $width = null)
    {
        if (empty($text)) {
            return array();
        }
        $lines = '';
        $currentLine = '';
        // calculate the page's width with respect to the margins
        if (empty($width)) {
            $width = $page->getWidth() - $this->margin['left'] - ($page->getWidth() - $this->margin['right']);
        }
        // regular expression that splits on whitespaces and dashes based on http://stackoverflow.com/a/11758732/719023
        $textChunks = preg_split('/([^\s-]+[\s-]+)/', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($textChunks as $textChunk) {
            $textChunk = trim($textChunk);
            if ($this->widthForStringUsingFontSize($currentLine . ' '
                    . $textChunk, $font, $fontSize) < $width
            ) {
                // do not add whitespace on first line
                if (!empty($currentLine)) {
                    $currentLine .= ' ';
                }
                $currentLine .= $textChunk;
            } else {
                // text is too broad, so add new line character
                $lines .= $currentLine . "\n";
                $currentLine = $textChunk;
            }
        }
        // append the last line
        $lines .= $currentLine;
        return explode("\n", $lines);
    }

    /**
     * Fix the percentage for taxes which come with four decimal places
     * from magento core.
     *
     * @param  string $label tax label which contains the badly formatted tax percentage
     *
     * @return string
     */
    protected function fixNumberFormat($label) {
        $pattern = "/(.*)\((\d{1,2}\.\d{4}%)\)/";
        if (preg_match($pattern, $label, $matches)) {
            $percentage = Zend_Locale_Format::toNumber(
                $matches[2],
                array(
                    'locale'    => Mage::app()->getLocale()->getLocale(),
                    'precision' => 2,
                )
            );

            return $matches[1] . '(' . $percentage . '%)';
        }

        return $label;
    }

    /**
     * get bold font file
     *
     * @return string
     */
    protected function getBoldFontFile()
    {
        return Mage::helper('firegento_pdf')->getFontPath() . DS
        . $this->getBoldFont();
    }

    /**
     * get bold font path
     *
     * @return string
     */
    protected function getBoldFont()
    {
        return Mage::getStoreConfig(
            FireGento_Pdf_Helper_Data::XML_PATH_BOLD_FONT
        );
    }

    /**
     * check whether font file exists for bold font
     *
     * @return bool
     */
    protected function boldFontFileExists()
    {
        return file_exists($this->getBoldFontFile());
    }

    /**
     * get italic font path
     *
     * @return string
     */
    protected function getItalicFont()
    {
        return Mage::getStoreConfig(
            FireGento_Pdf_Helper_Data::XML_PATH_ITALIC_FONT
        );
    }

    /**
     * check whether italic font file exists
     *
     * @return bool
     */
    protected function ItalicFontFileExists()
    {
        return file_exists($this->getItalicFontFile());
    }

    /**
     * get italic font file
     *
     * @return string
     */
    protected function getItalicFontFile()
    {
        return Mage::helper('firegento_pdf')->getFontPath() . DS
        . $this->getItalicFont();
    }


    /**
     * get the regular font path
     *
     * @return string
     */
    protected function getRegularFont()
    {
        return Mage::getStoreConfig(
            FireGento_Pdf_Helper_Data::XML_PATH_REGULAR_FONT
        );
    }

    /**
     * check whether font file exists for regular font
     *
     * @return bool
     */
    protected function regularFontFileExists()
    {
        return file_exists($this->getRegularFontFile());
    }

    /**
     * get the path to the font file for regular font
     *
     * @return string
     */
    protected function getRegularFontFile()
    {
        return Mage::helper('firegento_pdf')->getFontPath() . DS
        . $this->getRegularFont();
    }

    /**
     * @return string
     */
    private function getPageSize()
    {
        return Mage::helper('firegento_pdf')->getPageSizeConfigPath();
    }
}
