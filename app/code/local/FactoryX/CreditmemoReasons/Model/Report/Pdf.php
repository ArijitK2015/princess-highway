<?php
/**
 * Class FactoryX_CreditmemoReasons_Model_Report_Pdf
 */
class FactoryX_CreditmemoReasons_Model_Report_Pdf extends Mage_Core_Model_Abstract
{

    const MARGIN_LEFT = 40;
    const LINE_HEIGHT = 18;

    private $_x = 0;
    private $_y = 0;
    private $_columnPos = array();
    private $_renderBarcode = false;
    
    private $_columns;
    private $_collection;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
    public function __construct($columns, $collection)
    {
        parent::__construct();
        // other stuff
        $this->_columns = $_columns;
        $this->_collection = $_collection;
    }
     */    

    /**
    _pdfAddHeader

    Zend_Pdf_Font::FONT_TIMES
    Zend_Pdf_Font::FONT_TIMES_BOLD
    Zend_Pdf_Font::FONT_HELVETICA
    Zend_Pdf_Font::FONT_HELVETICA_BOLD

    .ttf | .tof
    reduce file size
    Zend_Pdf_Font::EMBED_DONT_EMBED
    Zend_Pdf_Font::EMBED_SUPPRESS_EMBED_EXCEPTION
    Zend_Pdf_Font::EMBED_DONT_COMPRESS
    combine bitwise OR e.g. $options = (BLAH1 | BLAH2)

    $font = Zend_Pdf_Font::fontWithPath($absolutePathToFont, $options);
    */
    private function _pdfAddHeader($page, $line, $underLine = false) {
        // set font
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $page->setFont($font, 12);
        $line = 2;
        $this->_y = $page->getHeight() - (self::LINE_HEIGHT * $line);
        $this->_x = self::MARGIN_LEFT;
        $this->_columnPos = array();

        $columnWidths = array(
            'reason'    => 100,
            'sku'       => 100,
            'barcode'   => 180,
            'qty'       => 50
        );

        foreach ($this->_columns as $column) {
            if ($column->getIndex() == 'sku') {
                $this->_renderBarcode = true;
            }
            Mage::helper('creditmemoreasons')->log(sprintf("%s->%s", __METHOD__, print_r($column->getData(), true)) );
            if ($column->getIsSystem()) {
                next;
            }

            $header = $column->getExportHeader();
            if (preg_match("/quantity/i", $header)) {
                $header = "Qty";
            }

            // drawText(string $text, float $x, float $y, [string $charEncoding = ''])
            $page->drawText($header, $this->_x, $this->_y);
            $this->_columnPos[$column->getIndex()] = $this->_x;

            $width = $font->widthForGlyph($font->glyphNumberForCharacter($header));
            $this->_x += (($width / $font->getUnitsPerEm() * 12) * strlen($header)) + $columnWidths[$column->getIndex()];

            if ($column->getIndex() == 'sku' && $this->_renderBarcode) {
                $header = "Barcode";
                $page->drawText($header, $this->_x, $this->_y);
                $this->_columnPos['barcode'] = $this->_x;

                $width = $font->widthForGlyph($font->glyphNumberForCharacter($header));
                $this->_x += (($width / $font->getUnitsPerEm() * 12) * strlen($header)) + $columnWidths['barcode'];
            }
        }

        if ($underLine) {
            $color = new Zend_Pdf_Color_Rgb($r = 0, $g = 0, $b = 0);
            // Zend_Pdf_Color
            $page->setLineColor($color);
            $page->setLineWidth(1);
            //$page->setLineDashingPattern($pattern, $phase = 0);
            // drawLine($x1, $y1, $x2, $y2);
            $this->_y = $this->_y - (self::LINE_HEIGHT / 2);
            $page->drawLine(self::MARGIN_LEFT, $this->_y, ($page->getWidth() - self::MARGIN_LEFT), $this->_y);
            $this->_y = $this->_y - self::LINE_HEIGHT;
        }
                
        return $line;
    }

    /**
    */
    public function generate($columns, $collection) {
        
        $this->_columns = $columns;
        $this->_collection = $collection;
        
        // load from template
        //$path = Zend_Pdf::load("/path/to/template.pdf");

        $pdf = new Zend_Pdf();
        
        //$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $pdf->pages[] = $page;
        // Remove specified page.
        //unset($pdf->pages[$id]);
        $pageIndex = 0;
        
        $line = $this->_pdfAddHeader($page, 2, $underLine = true);
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $page->setFont($font, 10);

        // double line hights for barcodes
        $this->_y = $page->getHeight() - ((self::LINE_HEIGHT * ($this->_renderBarcode ? 2 : 1)) * $line);

        foreach ($this->_collection as $row) {
            foreach ($this->_columns as $column) {
                if ($column->getIsSystem()) {
                    next;
                }
                $data = $row->getData($column->getIndex());

                if ($column->getIndex() == 'sku') {

                    /*
                    // temp image
                    $barcode = $this->_generateBarcode($data);
                    imagejpeg($barcode, 'barcode.jpg', 150);
                    imagedestroy($barcode);
                    $image = Zend_Pdf_Image::imageWithPath('barcode.jpg');
                    $barcodeHeight = 50;
                    $page->drawImage($image, $this->_columnPos[$column->getIndex()] + 150, $this->_y - $barcodeHeight, $x = 150, $this->_y);
                    */

                    $page->drawText($data, $this->_columnPos[$column->getIndex()], $this->_y);

                    $x2 = $this->_columnPos['barcode'];
                    // flip y
                    $y2 = $page->getHeight() - ($this->_y + self::LINE_HEIGHT);
                    $config = $this->_generateBarcode($data, $x2, $y2, $configOnly = true, $renderer = 'pdf');
                    //Mage::helper('creditmemoreasons')->log(sprintf("config=%s", print_r($config, true)) );
                    Zend_Barcode::factory($config)->setResource($pdf, $pageIndex)->draw();

                    /*
                    Zend_Barcode::factory(
                        'code128',
                        'pdf',
                        array('text' => "blah", 'barHeight'=> 40, 'fontSize' => 10),
                        array('topOffset' => 50))->setResource($pdf, $pageIndex)->draw();
                    */

                    // reset font
                    $page->setFont($font, 10);
                }
                else {
                    $page->drawText($data, $this->_columnPos[$column->getIndex()], $this->_y);
                }
            } // end row
            $line++;
            // double line hights for barcodes
            $this->_y = $page->getHeight() - ((self::LINE_HEIGHT * ($this->_renderBarcode ? 2 : 1)) * $line);
            Mage::helper('creditmemoreasons')->log(sprintf("%s->%s|y:%s", __METHOD__, $page->getHeight(), $this->_y) );

            if ($this->_y <= 150) {
                Mage::helper('creditmemoreasons')->log(sprintf("%s->new page!", __METHOD__) );
                $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                $page->setFont($font, 10);
                $pdf->pages[] = $page;
                $pageIndex++;
                
                $line = $this->_pdfAddHeader($page, 2, $underLine = true);
                $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
                $page->setFont($font, 10);
                
                // double line hights for barcodes
                $this->_y = $page->getHeight() - ((self::LINE_HEIGHT * ($this->_renderBarcode ? 2 : 1)) * $line);
                
            }
        }

        return $pdf;
    }

    /**
    format = code39 | code128
    renderer = pdf | image

    http://framework.zend.com/manual/1.12/en/zend.barcode.creation.html
    http://framework.zend.com/manual/1.12/en/zend.barcode.objects.html
    http://framework.zend.com/manual/1.12/en/zend.barcode.renderers.html
    */
    private function _generateBarcode($text, $x, $y, $configOnly = false, $renderer = 'image', $format = 'code128') {

        //a font is mandatory for Pdf
        Zend_Barcode::setBarcodeFont("/usr/share/fonts/truetype/arial.ttf");
        //Zend_Barcode::setBarcodeFont('/srv/magento/media/fonts/gothic.ttf');

        $barcodeParams = array(
            'text'          => $text,
            'barHeight'     => 40,
            'barThickWidth' => 3,
            'barThinWidth'  => 1,
            //'font'        => $font, // Font path to a TTF font or a number between 1 and 5
            //'fontSize'      => 12,
            'drawText'      => false,
            //'withBorder'    => true
            'withQuietZones'    => false,
            'factor'        => 1.5
        );

        $rendererParams = array(
            //'imageType'     => 'png',
            'leftOffset'    => $x,
            'topOffset'     => $y,
            //'horizontalPosition' => 'right',
            //'width'         => 150,
            //'height'        => 150
        );

        $config = new Zend_Config(array(
            'barcode'           => $format,
            'barcodeParams'     => $barcodeParams,
            'renderer'          => $renderer,
            'rendererParams'    => $rendererParams
        ));

        if ($configOnly) {
            return $config;
        }
        else {
            // Draw the barcode in a new image (resource #image)
            $barcode = Zend_Barcode::factory($config)->draw();
            // send the headers and the image
            //$barcode = Zend_Barcode::factory($config)->render();
            Mage::helper('creditmemoreasons')->log(sprintf("barcode=%s", print_r($barcode, true)) );
            return $barcode;
        }
    }

}