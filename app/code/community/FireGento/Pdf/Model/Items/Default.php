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
 * Default item model rewrite.
 *
 * @category  FireGento
 * @package   FireGento_Pdf
 * @author    FireGento Team <team@firegento.com>
 */
class FireGento_Pdf_Model_Items_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    /**
     * Draw item line.
     *
     * @param  int $position position of the product
     *
     * @return void
     */
    public function draw($position = 1)
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = array();

        $fontSize = 9;

        // draw Position Number
        /*
        $lines[0] = array(
            array(
                'text'      => $position,
                'feed'      => $pdf->margin['left'] + 10,
                'align'     => 'right',
                'font_size' => $fontSize
            )
        );
        */

        // draw SKU
        $lines[0][] = array(
            //'text' => Mage::helper('core/string')->str_split($this->getSku($item), 19),
            'text' => Mage::helper('core/string')->str_split($this->getSku($item), 25),
            //'feed' => $pdf->margin['left'] + 25,
            'feed' => $pdf->margin['left'] + 3,
            'font_size' => $fontSize
        );

        // draw Product name
        $lines[0][] = array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 40, true, true),
            'feed' => $pdf->margin['left'] + 90,
            'font_size' => $fontSize
        );

        $options = $this->getItemOptions();
        //Mage::helper('firegento_pdf')->log(sprintf("%s->options=%s", __METHOD__, print_r($options, true)) );
        if ($options) {
            foreach ($options as $option) {
                $optionTxt = $option['label'] . ': ';
                // append option value
                if (isset($option['value'])) {
                    $optionTxt .= isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                }
                $optionArray = $pdf->_prepareText($optionTxt, $page, $pdf->getFontRegular(), $fontSize, 215);
                $lines[][] = array(
                    'text' => $optionArray,
                    'feed' => $pdf->margin['left'] + 90 + 10
                );
            }
        }

        $columns = array();

        // prepare qty
        $columns['qty'] = array(
            'text'      => $item->getQty() * 1,
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_QTY
        );

        // prepare price
        $columns['price'] = array(
            'text'      => $order->formatPriceTxt($item->getPrice()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_PRICE
        );

        // prepare price_incl_tax
        $columns['price_incl_tax'] = array(
            'text'      => $order->formatPriceTxt($item->getPriceInclTax()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_PRICE_TAX
        );

        // prepare tax
        $columns['tax'] = array(
            'text'      => $order->formatPriceTxt($item->getTaxAmount() + $item->getHiddenTaxAmount()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TAX
        );

        // prepare tax_rate
        $columns['tax_rate'] = array(
            'text'      => round($item->getOrderItem()->getTaxPercent(), 2) . '%',
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TAX_RATE
        );

        // prepare total derived from row_total - discount_amount
        // note row_total != "Row Total" as shown on the invoice admin page
        $columns['total'] = array(
            'text'      => $order->formatPriceTxt($item->getRowTotal() - $item->getDiscountAmount()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TOTAL
        );

        // prepare subtotal = sales_flat_order_item.row_total
        $columns['subtotal'] = array(
            'text'      => $order->formatPriceTxt($item->getRowTotal()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TOTAL
        );

        // prepare subtotal_incl_tax
        $columns['subtotal_incl_tax'] = array(
            'text'      => $order->formatPriceTxt($item->getRowTotalInclTax()),
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TOTAL_TAX
        );

        // prepare discount_amount
        $val = "";
        if ($item->getDiscountAmount() > 0) {
            $val = $order->formatPriceTxt($item->getDiscountAmount());
        }
        $columns['discount_amount'] = array(
            'text'      => $val,
            'align'     => 'right',
            'font_size' => $fontSize,
            '_width'    => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_DISCOUNT
        );

        // draw columns in specified order
        $columnsOrder = explode(',', Mage::getStoreConfig('sales_pdf/invoice/item_price_column_order'));
        // draw starting from right
        $columnsOrder = array_reverse($columnsOrder);
        $columnOffset = 0;
        foreach ($columnsOrder as $columnName) {
            $columnName = trim($columnName);
            if (array_key_exists($columnName, $columns)) {
                $column = $columns[$columnName];
                $column['feed'] = $pdf->margin['right'] - $columnOffset;
                $columnOffset += $column['_width'];
                unset($column['_width']);
                $lines[0][] = $column;
            }
        }
/*
        if (Mage::getStoreConfig('sales_pdf/invoice/show_item_discount') && 0 < $item->getDiscountAmount()) {
            // print discount
            $text = Mage::helper('firegento_pdf')->__(
                'You get a %s%% discount of %s.',
                $item->getDiscountPercentage(),
                $order->formatPriceTxt($item->getDiscountAmount())
            );
            $lines[][] = array(
                'text'  => $text,
                //'align' => 'right',
                //'feed'  => $pdf->margin['right'] - $columnOffset
                // under the product options
                'align' => 'left',
                'feed' => $pdf->margin['left'] + 90
            );
        }
*/

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 15
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
