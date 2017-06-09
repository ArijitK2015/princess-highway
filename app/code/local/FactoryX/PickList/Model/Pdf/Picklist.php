<?php

/**
 * PDF class
 */
 
require_once('fpdf16/fpdf.php');

/**
 * Class FactoryX_PickList_Model_Pdf_Picklist
 */
class FactoryX_PickList_Model_Pdf_Picklist extends FPDF {
    
    // PickListPDF::PDF_LINE_H
    const PDF_LINE_H = 5;
    const BOX_W = 4;
    const PAGE_MAR_L = 10;
    const PAGE_MAX_H = 270;
    const PAGE_MAX_W = 200;
    
    protected $fromDate;
    protected $toDate;
    protected $status;
    protected $filter;
    protected $currentLine;

    /**
     * @param string $orientation
     * @param string $unit
     * @param string $format
     */
    public function _construct($orientation='P', $unit='mm', $format='A4') {
        Mage::helper('picklist')->log(sprintf("%s->%s", __METHOD__, $orientation));
        //parent::__construct($orientation='P', $unit='mm', $format='A4');
    }


    /**
    constructor
    */
    public function __construct() {
        Mage::helper('picklist')->log(__METHOD__);
        parent::__construct($orientation='P', $unit='mm', $format='A4');
    }
    
    /*
    function AcceptPageBreak() {}
    */

    /**
     * returns the current line
     * @param bool $newPage
     * @return
     */
    function getCurrentLine($newPage = false) {
        $currYPos = self::PDF_LINE_H * $this->currentLine;
        //Mage::helper('picklist')->log(sprintf("%s->line=%d [%d]", __METHOD__, $this->currentLine, $currYPos) );
        
        // should be handled by SetAutoPageBreak($auto = true, $bottomMargin = 10) ???
        if ($newPage && $currYPos > self::PAGE_MAX_H) {
            $this->AddNewPage();
        }
        return $this->currentLine;
    }

    /**
    gets the next line
     * @param int $increment
     * @param bool $newPage
     * @param string $debug
     * @return mixed
     */
    function getNextLine($increment = 1, $newPage = true, $debug = "") {
        $this->currentLine = $this->currentLine + $increment;
        $currYPos = self::PDF_LINE_H * $this->currentLine;
        /*
        if (!empty($debug)) {
            Mage::helper('picklist')->log(sprintf("%s->line=%d [%d|%d] - %s", __METHOD__, $this->currentLine, $currYPos, $newPage, $debug) );        
        }
        */
        // should be handled by SetAutoPageBreak($auto = true, $bottomMargin = 10) ???
        if ($newPage && $currYPos > self::PAGE_MAX_H) {
            $this->AddNewPage();
        }
        return $this->currentLine;
    }
    
    function AddNewPage() {
        //Mage::helper('picklist')->log(sprintf("%s->line=%d", __METHOD__, $this->currentLine) );
        $this->currentLine = 2.2;
        // start next line down
        if (!empty($this->filter)) {
            $this->currentLine = 3.2;
        }
        $this->AddPage('P');
    }
    
    //Page header
    function Header() {
        $this->SetY(5);
        // optional logo
        //$this->Image('logo_pb.png',10,8,33);
        
        //Arial bold 15
        $this->SetFont('Arial','B',10);
        
        //Move to the right
        //$this->Cell(1);
        //Title; w, h, text, border
        
        $helper = new FactoryX_PickList_Helper_Data();         
        $title = sprintf('%s :: Pick List - %s [%s - %s]',
            Mage::getStoreConfig('general/store_information/name'),
            $helper->getStatusLabel($this->status),
            $this->fromDate,
            $this->toDate
        );
        
        $this->Cell(95,5,$title,'B',0,'L');
        
        $dtime = new DateTime();
        $dtime->setTimeZone(new DateTimeZone("Australia/Victoria"));
        $this->Cell(95,5, $dtime->format("d/m/Y H:i:s"),'B',0,'R');
        
        if (!empty($this->filter)) {
            $this->SetY(10);
            $this->SetFont('Arial','B',8);
            //Mage::helper('picklist')->log(sprintf("%s->filter=%s", __METHOD__, $this->filter));            
            $this->Cell(95,5,$this->filter,'',0,'L');
        }
        $this->currentLine += 0.2;
    }
    
    /*
    // normal boring line
    $pdf->Line(
        PickListPDF::PAGE_MAR_L,
        PickListPDF::PDF_LINE_H * $pdf->getCurrentLine(),
        PickListPDF::PAGE_MAX_W,
        PickListPDF::PDF_LINE_H * $pdf->getCurrentLine()
    );
    */
    /**
     * @param float $width
     * @param int $dashWidth
     * @param int $gapWidth
     */
    function dashedLine($width = 0.2, $dashWidth = 4, $gapWidth = 2) {
        $this->SetLineWidth($width);

        $x1 = self::PAGE_MAR_L;
        $x2 = $x1 + $dashWidth;
        $y1 = self::PDF_LINE_H * $this->getCurrentLine();
        $y2 = $y1;
        $dashCnt = 1;
        while($x2 < self::PAGE_MAX_W ) {
            $this->Line($x1,$y1,$x2,$y2);
            $dashCnt++;
            $x1 += ($dashWidth + $gapWidth);
            $x2 = $x1 + $dashWidth;
            // draw last section
            if ($x2 >= self::PAGE_MAX_W) {
                $x2 = self::PAGE_MAX_W;
                $this->Line($x1,$y1,$x2,$y2);
            }
        }
        
        $this->SetLineWidth(0.2);
    }

    //Page footer
    function Footer() {
        //Position at 1.5 cm from bottom
        $this->SetY(-10);
        //Arial italic 8
        $this->SetFont('Arial','I',8);
        //Page number
        $this->Cell(0,10, sprintf("Page %s/{nb}", $this->PageNo()),0,0,'C');
    }

    /**
     * @param $date
     */
    function setFromDate($date) {
        //Mage::helper('picklist')->log("setFromDate(" . $date . ")");
        $this->fromDate = $date;
    }

    /**
     * @param $date
     */
    function setToDate($date) {
        //Mage::helper('picklist')->log("setToDate(" . $date . ")");
        $this->toDate = $date;
    }

    /**
     * @param $status
     */
    function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @param $filter
     */
    function setFilter($filter) {
        //Mage::helper('picklist')->log(sprintf("%s->filter=%s", __METHOD__, $filter));
        $this->filter = $filter;
    }

}