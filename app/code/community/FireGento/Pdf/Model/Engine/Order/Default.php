<?php
/**
 * Default order rendering engine.
 *
 * @category  FireGento
 * @package   FireGento_Pdf
 * @author    FireGento Team <team@firegento.com>
 */
class FireGento_Pdf_Model_Engine_Order_Default extends FireGento_Pdf_Model_Engine_Abstract
{

    /**
     * constructor to set mode to order
     */
    public function __construct()
    {
        parent::__construct();
        $this->setMode('order');
    }

    /**
     * Return PDF document
     *
     * @param  array $orders orders to render pdfs for
     *
     * @return Zend_Pdf
     */
    public function getPdf($orders = array())
    {
        $currentStore = Mage::app()->getStore()->getCode();
        $this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);

        foreach ($orders as $order) {
            // pagecounter is 0 at the beginning, because it is incremented in newPage()
            $this->pagecounter = 0;
            if ($order->getStoreId()) {
                Mage::app()->getLocale()->emulate($order->getStoreId());
                Mage::app()->setCurrentStore($order->getStoreId());
            }
            $order = Mage::getModel('sales/order')->load($order->getId());
            //$order = $order->getOrder();
            $this->setOrder($order);

            $page = $this->newPage();

            $this->insertAddressesAndHeader($page, $order, $order);

            $this->_setFontRegular($page, 9);
            $this->insertTableHeader($page);

            $this->y -= 20;

            $position = 0;

            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                $item->setOrderItem($item);
                
                // Ln() ???
                $showFooter = Mage::getStoreConfig('sales_pdf/firegento_pdf/show_footer');
                if ($this->y < 50 || ($showFooter == 1 && $this->y < 100)) {
                    $page = $this->newPage(array());
                }
                $position++;
                $page = $this->_drawItem($item, $page, $order, $position);

                // draw line between items
                if (Mage::getStoreConfig('sales_pdf/firegento_pdf/show_lines_between_items')) {
                    $page->setLineColor($this->colors['grey1']);
                    $page->drawLine($this->margin['left'], $this->y, $this->margin['right'], $this->y);
                    $this->Ln(15);
                }
            }

            /* add line after items */
            //$page->drawLine($this->margin['left'], $this->y + 5, $this->margin['right'], $this->y + 5);
            $page->drawLine($this->margin['left'], $this->y + 10, $this->margin['right'], $this->y + 10);

            /* add totals */
            $order->setOrder($order);
            $page = $this->insertTotals($page, $order);

            /* add note */
            $page = $this->_insertNote($page, $order, $order);

            // Add footer
            $this->_addFooter($page, $order->getStore());

            if ($order->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }

        // Revert back to the original current store
        Mage::app()->setCurrentStore($currentStore);

        $this->_afterGetPdf();

        return $pdf;
    }

    /**
     * Insert Table Header for Items
     *
     * @param  Zend_Pdf_Page &$page current page object of Zend_PDF
     *
     * @return void
     */
    protected function insertTableHeader(&$page)
    {
        $page->setFillColor($this->colors['grey1']);
        $page->setLineColor($this->colors['grey1']);
        $page->setLineWidth(1);
        $page->drawRectangle($this->margin['left'], $this->y, $this->margin['right'], $this->y - 15);

        $page->setFillColor($this->colors['black']);
        $font = $this->_setFontRegular($page, 9);

        $this->y -= 11;
        /*
        $page->drawText(
            Mage::helper('firegento_pdf')->__('Pos'), $this->margin['left'] + 3, $this->y, $this->encoding
        );
        */
        $page->drawText(
            //Mage::helper('firegento_pdf')->__('No.'),
            Mage::helper('firegento_pdf')->__('Product Code'),
            //$this->margin['left'] + 25,
            $this->margin['left'] + 3, $this->y, $this->encoding
        );
        $page->drawText(
            //Mage::helper('firegento_pdf')->__('Description'),
            Mage::helper('firegento_pdf')->__('Product Description'),
            $this->margin['left'] + 90, $this->y, $this->encoding
        );

        $columns = array();
        $columns['price'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Price'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_PRICE
        );
        $columns['price_incl_tax'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Price (+ tax)'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_PRICE_TAX
        );
        $columns['qty'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Qty'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_QTY
        );
        $columns['tax'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Tax'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TAX
        );
        $columns['tax_rate'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Tax rate'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TAX_RATE
        );
        $columns['subtotal_incl_tax'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Total (+ tax)'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TOTAL_TAX
        );
        $columns['discount_amount'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Discount'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_DISCOUNT
        );

        // total = sales_flat_order_item.row_total - sales_flat_order_item.discount
        $columns['total'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Total'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_TOTAL
        );

        // subtotal = sales_flat_order_item.row_total
        $columns['subtotal'] = array(
            'label'  => Mage::helper('firegento_pdf')->__('Subtotal'),
            '_width' => FireGento_Pdf_Model_Engine_Invoice_Default::INVOICE_COLUMN_WIDTH_SUBTOTAL
        );

        // draw price, tax, and subtotal in specified order
        $columnsOrder = explode(',', Mage::getStoreConfig('sales_pdf/invoice/item_price_column_order'));
        // draw starting from right
        $columnsOrder = array_reverse($columnsOrder);
        $columnOffset = 0;
        $columnCnt = 0;
        foreach ($columnsOrder as $columnName) {
            $columnName = trim($columnName);
            //Mage::helper('firegento_pdf')->log(sprintf("%s->columnName=%s", __METHOD__, $columnName) );
            if (array_key_exists($columnName, $columns)) {
                $column = $columns[$columnName];
                // last columns
                $columnCnt++;
                //Mage::helper('firegento_pdf')->log(sprintf("%s->var=%s[%s==%s]", __METHOD__, $label, $columnCnt, count($columns)) );
                $labelWidth = $this->widthForStringUsingFontSize($column['label'], $font, 9);
                if ($columnCnt == 1) {
                    $labelWidth += 3;
                }
                $page->drawText(
                    $column['label'],
                    $this->margin['right'] - $columnOffset - $labelWidth,
                    $this->y,
                    $this->encoding
                );
                $columnOffset += $column['_width'];
            }
        }
    }

    /**
     * Initialize renderer process
     *
     * @param  string $type renderer type to be initialized
     *
     * @return void
     */
    protected function _initRenderer($type)
    {
        //parent::_initRenderer($type);

        // Initialize renderer process
        //$node = Mage::getConfig()->getNode('global/pdf/' . $type);
        $node = Mage::getConfig()->getNode('global/pdf/invoice');
        foreach ($node->children() as $renderer) {
            $this->_renderers[$renderer->getName()] = array(
                'model'     => (string)$renderer,
                'renderer'  => null
            );
        }

        $this->_renderers['default'] = array(
            'model'    => 'firegento_pdf/items_default',
            'renderer' => null
        );
        $this->_renderers['grouped'] = array(
            'model'    => 'firegento_pdf/items_grouped',
            'renderer' => null
        );
        $this->_renderers['bundle'] = array(
            'model'    => 'firegento_pdf/items_bundle',
            'renderer' => null
        );
        $this->_renderers['downloadable'] = array(
            'model'    => 'firegento_pdf/items_downloadable',
            'renderer' => null
        );
    }

}
