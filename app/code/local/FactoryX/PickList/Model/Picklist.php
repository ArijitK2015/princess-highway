<?php
/**
image
http://blog.chapagain.com.np/magento-resize-image/
*/

/*
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
*/

$font_loc = "arialbd.ttf";

//class PickList {
/**
 * Class FactoryX_PickList_Model_Picklist
 */
class FactoryX_PickList_Model_Picklist {

    protected $pdfDir;
    protected $giftWrapImage;

    /*
    self::FPDF_BORDER_ON
    self::FPDF_BORDER_OFF
    self::FPDF_ALIGN_LEFT
    self::FPDF_ALIGN_RIGHT
    self::FPDF_ALIGN_CENTER
    self::FPDF_FILL_ON
    self::FPDF_FILL_OFF
    self::FPDF_LN_RIGHT
    self::FPDF_LN_START
    self::FPDF_LN_BELOW
    */

    const DATE_FORMAT = 'Y-m-d H:i:s';

    const IMG_BARCODE_W = 60;
    const IMG_BARCODE_H = 10;

    const IMG_GIFTWRP_W = 14;
    const IMG_GIFTWRP_H = 10;

    const FPDF_BORDER_ON = true;
    const FPDF_BORDER_OFF = false;
    const FPDF_ALIGN_LEFT = "L";
    const FPDF_ALIGN_RIGHT = "R";
    const FPDF_ALIGN_CENTER = "C";
    const FPDF_FILL_ON = true;
    const FPDF_FILL_OFF = false;
    const FPDF_LN_RIGHT = 0;
    const FPDF_LN_START = 1;
    const FPDF_LN_BELOW = 2;

    // see sales_flat_order_item
    const ORDER_ITEM_FIELD_PREORDER     = "pre_order";
    const ORDER_ITEM_FIELD_CONSOLIDATED = "online_only";

    /*
    fx_exp
    ZZ22220500 EXP POST 500g BAG
    ZZ22223000 EXP POST 3KG BAG
    ZZ22225000 EXP POST 5KG BAG
    df_exp
    ZZD2220500 DF ONLINE EXP POST 500g BAG
    ZZD2223000 DF ONLINE EXP POST 3KG BAG
    ZZD2225000 DF ONLINE EXP POST 5KG BAG
    fx_exp_free
    ZZ22200001 FREE EXP POST 500g BAG
    ZZ22200002 FREE EXP POST 3KG BAG
    ZZ22200003 FREE EXP POST 5KG BAG
    fx_std
    ZZ22200500 STD POST 500g BAG
    ZZ22203000 STD POST 3KG BAG
    ZZ22205000 STD POST 5KG BAG
    fx_int
    ZZ22220000 INTERNATIONAL SHIPPING
    fx_int_free
    ZZ22200000 FREE SHIPPING INTERNATIONAL
    fx_eparcel_std
    ZZ2224-0001 E PARCEL - STANDARD SHIPPING SERVICES
    fx_eparcel_exp
    ZZ2224-0002 E PARCEL - EXPRESS SHIPPING SERVICS
    */

    protected $accpacShippingCodes = array(
        // flatrate_flatrate
        "fx_exp" => array(
            //"5000" => "zz22225000",
            //"3000" => "zz22223000",
            "0500" => "zz22220500"
        ),
        "df_exp" => array(
            //"5000" => "zzd2225000",
            //"3000" => "zzd2223000",
            "0500" => "zzd2220500"
        ),
        "fx_exp_free" => array(
            //"5000" => "zz22200003",
            //"3000" => "zz22200002",
            "0500" => "zz22200001"
        ),
        // freeshipping_freeshipping
        "fx_std"  => array(
            //"5000" => "zz22205000",
            //"3000" => "zz22203000",
            "0500" => "zz22200500"
        ),
        // tablerate_bestway
        "fx_int" => array(
            "5000" => "zz22220000"
        ),
        "fx_int_free" => array(
            "5000" => "zz22200000"
        ),
        // tablerate_bestway NZ
        "fx_nz" => array(
            "5000" => "zz22220015"
        ),
        "fx_nz_free" => array(
            "5000" => "zz22200015"
        ),
        // eparcel
        "fx_eparcel_std"  => array(
            "0" => "ZZ2224-0001"
        ),
        "fx_eparcel_exp"  => array(
            "0" => "ZZ2224-0002"
        ),
        // temando
        "temando"        => array(
            "0" => "temando"
        )
    );

    /**
    item amounts used for testing purposes
    */
    protected $itemAmounts = array(
        "price","base_price","original_price","base_original_price",
        "tax_percent","tax_amount","base_tax_amount","tax_invoiced",
        "base_tax_invoiced","discount_percent","discount_amount",
        "base_discount_amount","discount_invoiced","base_discount_invoiced",
        "row_total","base_row_total","row_invoiced","base_row_invoiced",
        "base_tax_before_discount","tax_before_discount","price_incl_tax",
        "base_price_incl_tax","row_total_incl_tax","base_row_total_incl_tax",
        "hidden_tax_amount","base_hidden_tax_amount","hidden_tax_invoiced",
        "base_hidden_tax_invoiced"
    );

    /**
    */
    protected $accpacGiftBoxes = array(
        "sml" => "AZ10000000000S",
        "med" => "AZ10000000000M",
        "lrg" => "AZ10000000000L"
    );

    protected $white = array(255,255,255);
    protected $black = array(0,0,0);
    protected $red = array(255,0,0);
    protected $orange = array(255,128,0);
    protected $blue = array(51,153,255);
    protected $green = array(51,255,51);
    protected $grey = array(240,240,240);

    protected $defaultColourField = "colour";
    protected $defaultSizeField = "size";
    protected $defaultWeightField = "weight";

    protected $colourOptions = array();

    protected $colourFields = array(
        "ah" => "colour_all",
        "cm" => "color",
        "df" => "colour_description",
        "fx" => "colour",
        "go" => "colour",
        "jl" => "colour_description",
        "lv" => "colour_description"
    );

    protected $brandShow = array(
        "ah" => false,
        "cm" => false,
        "df" => true,
        "fx" => true,
        "go" => false,
        "jl" => false,
        "lv" => false
    );

    protected $brandFields = array(
        //"fx" => "manufacturer",
        "df" => "brand"
    );

    protected $fromDisplay;
    protected $toDisplay;
    protected $status;
    protected $includeImage;
    protected $includeSummary;
    protected $includeZero;
    protected $helper;

    protected $totalOrderCnt;
    protected $totalItemCnt;
    protected $shippingPaidTotal;
    protected $grandTotal;
    protected $totalDiscounts;

    /**
     *
     */
    public function __construct() {
        //$this->helper->log("construct()");
    }

    /*
    times are entered as locale times

    $config = array(
        // filter
        order_from
        order_to
        order_source
        order_state
        order_status
        include_image
        include_zero
        filter_region
        filter_region_apply
        // output
        sort_by
        number_pdf
    )

    @param array $config config array
    @return array $pdfs arrays of pdfs
    */
    /**
     * @param $config
     * @return array
     * @throws Exception
     */
    public function generate($config) {
        //$this->helper->log(sprintf("%s->%s", __METHOD__, print_r($config, true)));
        $sortby = "";

        $ts_from = $config['order_from'];
        $ts_to = $config['order_to'];
        $source = $config['order_source'];
        $state = $config['order_state'];
        $this->status = $config['order_status'];
        $this->includeImage = $config['include_image'];
        $this->includeSummary = $config['include_summary'];

        if (array_key_exists('sort_by', $config)) {
            $sortby = $config['sort_by'];
        }

        $this->helper = new FactoryX_PickList_Helper_Data();

        $this->fromDisplay = date(self::DATE_FORMAT, $ts_from);
        $this->toDisplay = date(self::DATE_FORMAT, intval($ts_to));

        // catch date error wierdness 1970
        if (preg_match("/1970/", $this->fromDisplay) || preg_match("/1970/", $this->toDisplay)) {
            throw new Exception(sprintf("date exception [%s - %s]", $this->fromDisplay, $this->toDisplay));
        }

        // get gift wrap image
        $this->giftWrapImage = sprintf("%s/skin/adminhtml/default/default/factoryx/images/gift_boxes.png", Mage::getBaseDir('skin'));

        /*
        ini_set('auto_detect_line_endings', true);
        $auto_detect_line_endings = ini_get('auto_detect_line_endings');
        $this->helper->log(sprintf("auto_detect_line_endings=%s", $auto_detect_line_endings));
        */

        $this->filterDesc = "";
        $this->totalOrderCnt = 0;
        $this->totalItemCnt = 0;
        $this->shippingPaidTotal = 0;
        $this->grandTotal = 0;
        $this->totalDiscounts = 0;

        $orderFilter = array(
            'date_from'     => $config['order_from'],
            'date_to'       => $config['order_to'],
            'source'        => $config['order_source'],
            'state'         => $config['order_state'],
            'status'        => $config['order_status'],
            'filter_region'         => $config['filter_region'],
            'filter_region_apply'   => $config['filter_region_apply'],
            'filter_cg'             => $config['filter_cg'],
            'filter_cg_apply'       => $config['filter_cg_apply'],
            'filter_pt'             => $config['filter_pt'],
            'filter_pt_apply'       => $config['filter_pt_apply'],
            'include_zero'          => $config['include_zero'],
            'sort_by'               => $sortby
        );
        //$this->helper->log(sprintf("config=%s", print_r($orderFilter, true)) );
        $orders = $this->getOrders($orderFilter);
        $this->helper->log(sprintf("orders=%s", count($orders)) );

        // divide into groups
        $breakCnt = array();
        $groups[] = $orders;
        if ($config['number_pdf'] > 1) {
            $groups = $this->helper->array_chunk_balance($orders, $config['number_pdf']);
        }
        $pdfs = array();
        foreach($groups as $group) {
            $pdfs[] = $this->createPDF($group);
        }
        return $pdfs;
    }

    /**
     *
     * @param array $rows
     * @return a file
     * @param $rows
     * @return bool|string
     */
    private function createPDF($rows) {

        setlocale(LC_TIME, "en_AU");

        // $pdf = new FactoryX_PickList_Model_Pdf_Picklist();
        //$pdf = Mage::getModel('picklist/pdf_picklist', array('orientation' => 'P') );
        $pdf = Mage::getModel('picklist/pdf_picklist');

        $pdf->SetAutoPageBreak($auto = true, $bottomMargin = 10);

        $pdf->setFromDate($this->fromDisplay);
        $pdf->setToDate($this->toDisplay);
        $pdf->setStatus($this->status);
        $pdf->setFilter($this->filterDesc);

        $pdf->AliasNbPages();
        $pdf->AddNewPage();
        $pdf->SetFont('Arial','',8);

        $totalItemCnt = 0;
        //$pdf->Cell(40,10,'');
        foreach ($rows as $row) {
            //$this->helper->log(sprintf("%s->partial=%s", __METHOD__, $row['partial']));
            $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine());

            $source = "m";
            //$this->helper->log(sprintf("%s->source=%s,%s", __METHOD__, $row["source"], stripos($row["source"],"ebay"))) );
            if (stripos($row["source"], "ebay") !== false) {
                $source = "e";
            }

            // highlight partials
            if ($row["partial"]) {
                //$pdf->SetDrawColor($this->red[0],$this->red[1],$this->red[2]);
                $pdf->SetFillColor($this->red[0],$this->red[1],$this->red[2]);
            }
            else {
                $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
            }
            //$pdf->Cell(22, 5, sprintf("#%s(%s)", $row["number"], $source), self::FPDF_BORDER_ON);

            /**
            ORDER HEADER
            **/
            // Defines the line width. By default, the value equals 0.2 mm.
            // Line(float x1, float y1, float x2, float y2)
            $pdf->dashedLine(1.2);
            $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(0.1, false));
            //$this->helper->log(sprintf("%s->ypos=%s", __METHOD__, $ypos) );

            $pdf->SetLineWidth(0.2);
            $pdf->Cell(22, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, sprintf("#%s", $row["number"]),
                self::FPDF_BORDER_ON,
                self::FPDF_LN_RIGHT,
                self::FPDF_ALIGN_LEFT,
                self::FPDF_FILL_ON
            );

            $pdf->SetDrawColor(0,0,0);
            $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(48, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $row["name"], self::FPDF_BORDER_ON);
            $pdf->SetFont('Arial','',8);

            $width = 120;
            //if ($row["company"]) {
            $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $row["company"], self::FPDF_BORDER_ON);
            $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, substr($row["method"], 0, 40), self::FPDF_BORDER_ON);
            $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());

            $pdf->Cell(70, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, sprintf("E:%s T:%s", $row["email"], $row["phone"]), self::FPDF_BORDER_ON);
            $pdf->Cell(120, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $row["address"], self::FPDF_BORDER_ON);
            $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(1, false, "name"));

            $itemCnt = 0;

            if (is_array($row["wrap"])) {
                $itemCnt++;

                $sku = $this->accpacGiftBoxes["sml"];
                $toShip = 0;
                $name = "gift wrapping service";
                $outLine = false;

                $giftM = sprintf("Message: %s", $row["wrap"]['message']);
                $giftF = sprintf("From: %s", $row["wrap"]['sender']);
                $giftT = sprintf("  To: %s", $row["wrap"]['recipient']);

                $pdf->SetFont('Courier','', 8);

                $barCode = self::generateBarcode($sku);
                if ($this->giftWrapImage && is_file($this->giftWrapImage)) {
                    $pdf->Image($this->giftWrapImage,
                        FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L,
                        FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(),
                        self::IMG_GIFTWRP_W,
                        self::IMG_GIFTWRP_H
                    );
                }
                $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L + self::IMG_GIFTWRP_W, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine());

                /**
                */
                $lineLength = 100;
                $break = "\n";
                $wrapped = wordwrap($giftM, $lineLength, $break);
                $lines = explode($break, $wrapped);

                $pdf->Cell(5, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "", ($outLine?"LT":self::FPDF_BORDER_OFF), 0, 'L', true);
                $pdf->Cell(58, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $giftF, ($outLine?"LT":self::FPDF_BORDER_OFF), 0, 'L', true);
                $pdf->Cell(58, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $giftT, ($outLine?"LT":self::FPDF_BORDER_OFF), 0, 'L', true);

                $pdf->Cell(55, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "box used [sml|med|lrg]", ($outLine?"LT":self::FPDF_BORDER_OFF), 0, 'L', true);

                $pdf->getNextLine(0.1, false);

                $pdf->Rect(185, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), FactoryX_PickList_Model_Pdf_Picklist::BOX_W, FactoryX_PickList_Model_Pdf_Picklist::BOX_W, "DF");
                $pdf->Rect(190, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), FactoryX_PickList_Model_Pdf_Picklist::BOX_W, FactoryX_PickList_Model_Pdf_Picklist::BOX_W, "DF");
                $pdf->Rect(195, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), FactoryX_PickList_Model_Pdf_Picklist::BOX_W, FactoryX_PickList_Model_Pdf_Picklist::BOX_W, "DF");

                $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L + self::IMG_GIFTWRP_W + 5, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(1, false));
                // print each line of the message
                foreach ($lines as $i => $line) {
                    $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $line, ($outLine?"TR":self::FPDF_BORDER_OFF), 0, 'L', true);
                    $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L + self::IMG_GIFTWRP_W + 5, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(1, false, $line));
                }

                /*
                $pdf->Cell(48, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "", ($outLine?"LB":self::FPDF_BORDER_OFF), 0, 'L', true);
                $pdf->Cell(52, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "", ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);
                $pdf->Cell(30, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "", ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);
                */
                //$pdf->getNextLine();
                $pdf->SetFont('Arial','',8);
            }

            $addedShippingHeader = false;
            foreach ($row["items"] as $item) {
                $toShip = (array_key_exists('toShip', $item))?$item['toShip']:0;
                $itemCnt++;
                // alternate colour
                if ($itemCnt % 2 == 0) {
                    $pdf->SetFillColor($this->grey[0],$this->grey[1],$this->grey[2]);
                }
                else {
                    $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
                }
                if (array_key_exists("deleted", $item) && $item['deleted']) {
                    $pdf->SetFillColor($this->orange[0],$this->orange[1],$this->orange[2]);
                }
                if (array_key_exists("online", $item) && $item['online']) {
                    $pdf->SetFillColor($this->green[0],$this->green[1],$this->green[2]);
                }
                if (array_key_exists("preorder", $item) && $item['preorder']) {
                    $pdf->SetFillColor($this->blue[0],$this->blue[1],$this->blue[2]);
                }

                // start rendering item
                $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine());

                //Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
                /**
                Start shipping section
                **/
                if ($item['brand'] == "shipping" && !$addedShippingHeader) {
                    $addedShippingHeader = true;
                    //$pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
                    //$pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine());
                    //$pdf->Cell(0, 8, "*** POST BAGS USED ***", 1, 2, 'C', true);
                    //$pdf->Line(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine());
                    //$pdf->Rect(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), 190, 1);
                    $pdf->dashedLine(0.6);
                    $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(0.1, false));
                }

                if ($item['barcode'] && is_file($item['barcode'])) {
                    $pdf->Image($item['barcode'], FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L,  FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), self::IMG_BARCODE_W, self::IMG_BARCODE_H);
                }
                $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L + self::IMG_BARCODE_W, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine());

                //$this->helper->log(sprintf("%s->item=%s", __METHOD__, print_r($item, true)) );
                $name = $this->getItemName($item);

                $outLine = false;
                $toShip = (array_key_exists('toShip', $item))?$item['toShip']:0;
                if (array_key_exists("partial", $row) && $row["partial"]) {
                    //$pdf->SetDrawColor($this->red[0],$this->red[1],$this->red[2]);
                    // $status... !preg_match("/ship/i", $name)
                    // processing_partial = processing_part_shipped|processing_part_shipped_nt|processing_shipped_nt|processing_stage2
                    $partialStatus = "processing_partial|processing_stage2";
                    //$this->helper->log(sprintf("%s->status=%s,%s", __METHOD__, $status, $partialStatus) );
                    //preg_match(sprintf("/%s/i", $partialStatus), $status) &&
                    if ($toShip > 0 && !preg_match("/ship/i", $name)) {
                        $outLine = true;
                    }
                }

                // layout shipping item differently
                if ($item['brand'] == "shipping") {
                    $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $name, ($outLine?"LT":self::FPDF_BORDER_OFF), 0, 'L', true);
                    $pdf->SetFont('Arial','B', 10);
                    /*
                    $pdf->Cell(8, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "", ($outLine?"T":self::FPDF_BORDER_OFF), 0, 'L', true);
                    $pdf->SetFont('Arial','B', 8);
                    $pdf->Cell(82, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "", ($outLine?"TR":self::FPDF_BORDER_OFF), 0, 'L', true);
                    */
                }
                else {
                    $totalItemCnt += $toShip;
                    $pdf->Cell(40, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $item['sku'], ($outLine?"LT":self::FPDF_BORDER_OFF), 0, 'L', true);
                    $pdf->SetFont('Arial','B', 10);
                    $pdf->Cell(8, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, number_format($toShip, 0), ($outLine?"T":self::FPDF_BORDER_OFF), 0, 'L', true);
                    $pdf->SetFont('Arial','B', 8);
                    $pdf->Cell(82, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $name, ($outLine?"TR":self::FPDF_BORDER_OFF), 0, 'L', true);
                }

                /** show how many are left */
                if (array_key_exists("online", $item) && $item['online']) {
                    $stock = sprintf("qty %d", $item['stock']);
                    $pdf->SetFont('Arial','B', 6);
                    $pdf->Cell(9, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $stock, ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);
                }

                $pdf->SetFont('Arial','', 8);

                if ($item['brand'] == "shipping") {
                    $pdf->SetXY(70, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(1, false));
                    // EMPTY CELL
                    $x = FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAX_W - (self::IMG_BARCODE_W + 10);
                    $pdf->Cell($x, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "", ($outLine?"LB":self::FPDF_BORDER_OFF), 0, 'L', true);
                }
                else {
                    /**
                    PRICE LINE
                    **/
                    $pdf->SetXY(70, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(1, false, $name));

                    // 1st section:  price & tax e.g. $20.50 (0.00)
                    $tax = (array_key_exists('tax', $item)) ? $item['tax'] : 0;
                    $price = (array_key_exists('price1', $item)) ? $item['price1'] : 0;
                    $tPrice = sprintf("$%0.2f (%0.2f)", number_format($price, 2), number_format($tax, 2));
                    $pdf->Cell(48, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $tPrice, ($outLine?"LB":self::FPDF_BORDER_OFF), 0, 'L', true);

                    // 2nd section:  price e.g. $20.50
                    $price = (array_key_exists('price1', $item)) ? $item['price2'] : 0;
                    $tPrice = sprintf("$%0.2f", number_format($price, 2));
                      $pdf->Cell(52, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $tPrice, ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);

                    // 3rd section of price line
                    if (array_key_exists('discount', $item) && $item['discount'] && $item['discount'] > 0) {
                        $tPrice = sprintf("%d%%", number_format($item['discount'], 0));
                        $pdf->Cell(30, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $tPrice, ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);
                    }
                    else {
                        // EMPTY CELL
                        $pdf->Cell(30, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "", ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);
                    }
                }

                $pdf->SetDrawColor(0,0,0);

                /**
                DRAW 3 OR 1 BLANK BOX(ES) - for ticking
                **/
                //$pdf->SetXY($pdf->GetX() - 5, $pdf->GetY());
                $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
                if ($item['brand'] == "shipping") {
                    $pdf->Rect(185, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), FactoryX_PickList_Model_Pdf_Picklist::BOX_W, FactoryX_PickList_Model_Pdf_Picklist::BOX_W, "DF");
                    $pdf->Rect(190, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), FactoryX_PickList_Model_Pdf_Picklist::BOX_W, FactoryX_PickList_Model_Pdf_Picklist::BOX_W, "DF");
                }

                if ($toShip > 1) {
                    $startPos = 195;
                    $maxBoxes = 10;
                    $i = 0;
                    for(; ($i < $toShip && $i < $maxBoxes); $i++) {
                        $pdf->Rect($startPos - ($i * 5), FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), FactoryX_PickList_Model_Pdf_Picklist::BOX_W, FactoryX_PickList_Model_Pdf_Picklist::BOX_W, "DF");
                    }
                    // add a big plus if > $maxBoxes
                    if ($toShip > $maxBoxes) {
                        // alternate colour
                        if ($itemCnt % 2 == 0) {
                            $pdf->SetFillColor($this->grey[0],$this->grey[1],$this->grey[2]);
                        }
                        else {
                            $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
                        }
                        $pdf->SetFont('Arial','B', 12);
                        $pdf->SetX($startPos - ($maxBoxes * 5));
                        $pdf->Cell(4, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "+", 0, 0, 'L', true);
                        $pdf->SetFont('Arial','', 8);
                    }
                }
                else {
                    $pdf->Rect(195, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine(), FactoryX_PickList_Model_Pdf_Picklist::BOX_W, FactoryX_PickList_Model_Pdf_Picklist::BOX_W, "DF");
                }
                //$pdf->Cell(4, 4, " ", self::FPDF_BORDER_ON, 0, 'L', true);

                // image
                if ($this->includeImage && $item['brand'] != "shipping") {
                    //$this->helper->log(sprintf("render:%s", $item['image']));
                    if ($item['image'] && is_file($item['image'])) {
                        $info = pathinfo($item['image']);
                        $pdf->Image($item['image'], 10, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(), 27, 30, $info['extension']);
                    }
                    /*
                    else {
                        $pdf->Rect(10, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine(), 27, 30, "D");
                    }
                    */
                    $pdf->getNextLine(5);
                }
                $pdf->getNextLine();
            }

            $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);

            $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getCurrentLine());

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(15, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Subtotal:", self::FPDF_BORDER_ON);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(17, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, sprintf("$%0.2f", number_format($row['subtotal'], 2)), self::FPDF_BORDER_ON);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(18, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Shipping:", self::FPDF_BORDER_ON);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(15, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, sprintf("$%0.2f", number_format($row['shipping'], 2)), self::FPDF_BORDER_ON);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Discount:", self::FPDF_BORDER_ON);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(58, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, '$' . number_format($row['discount'], 2) . " " . $row['code'], self::FPDF_BORDER_ON);
            $pdf->SetFont('Arial','B',8);

            $pdf->Cell(10, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Tax:", self::FPDF_BORDER_ON);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(10, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, '$' . number_format($row['tax'], 2), self::FPDF_BORDER_ON);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(10, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Total:", self::FPDF_BORDER_ON);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(17, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, '$' . number_format($row['total'], 2), self::FPDF_BORDER_ON);

            $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
            $time = strtotime($row['created_at']);
            $created_at = strftime("%d-%m-%Y %H:%M:%S%p", Mage::getModel('core/date')->timestamp($time));
            $time = strtotime($row['updated_at']);
            $updated_at = strftime("%d-%m-%Y %H:%M:%S%p", Mage::getModel('core/date')->timestamp($time));

            $info = sprintf("Ordered on: %s, Last updated: %s, Status: %s", $created_at, $updated_at, $this->helper->getStatusLabel($row['status']));
            $pdf->Cell(190, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $info, self::FPDF_BORDER_ON);

            $pdf->getNextLine(1.5);
        }

        // create stats page (new page)
        if ($this->includeSummary) {
            //$pdf->getNextLine(3);
            $averageItemsPerOrder = $this->totalItemCnt / $this->totalOrderCnt;
            //$averageItemsPerOrder = $this->totalItemCnt / count($rows);
            //$this->helper->log(sprintf("%s->averageItemsPerOrder=%d/%d=%d", __METHOD__, $this->totalItemCnt, $this->totalOrderCnt, $averageItemsPerOrder));
            $this->createSummary($pdf, array(
                'totalItemCnt'          => $totalItemCnt,
                'averageItemsPerOrder'  => $averageItemsPerOrder,
                'numberOfOrders'        => count($rows)
            ));
        }

        $tmpFile = self::secureTmpname(".pdf", "pickSheet");
        //$this->helper->log(sprintf("%s->tmpFile=%s", __METHOD__, $tmpFile));
        $pdf->Output($tmpFile, "F");

        return $tmpFile;
    }

    /**
     * @param $pdf
     * @param $summary
*/
    private function createSummary(&$pdf, $summary) {
        $pdf->AddNewPage();

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(190, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * 2, "Summary", self::FPDF_BORDER_OFF);
        $pdf->SetFont('Arial','B',8);
        $pdf->getNextLine();

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Total number of orders:", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $this->totalOrderCnt, 1);

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Number of orders (this sheet):", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $summary['numberOfOrders'], 1);

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Total number of items ordered:", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $this->totalItemCnt, 1);

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Number of items ordered (this sheet):", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, $summary['totalItemCnt'], 1);

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Av items per order:", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, number_format($summary['averageItemsPerOrder'], 2), self::FPDF_BORDER_ON);

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Gross Profit:", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, number_format($this->grandTotal - $this->shippingPaidTotal, 2), self::FPDF_BORDER_ON);

        // gross
        //$pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        //$pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Gross1 (no discount):", self::FPDF_BORDER_ON);
        //$pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, number_format($gross_profit, 2), self::FPDF_BORDER_ON);

        //$pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        //$pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Gross2 (no discount):", self::FPDF_BORDER_ON);
        //$pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, number_format($this->grandTotal - $this->shippingPaidTotal + ($this->totalDiscounts * -1), 2), self::FPDF_BORDER_ON);

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Gross Shipping:", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, number_format($this->shippingPaidTotal, 2), self::FPDF_BORDER_ON);

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Total Discount:", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, number_format($this->totalDiscounts, 2), self::FPDF_BORDER_ON);

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "Total Gross (inc shipping):", self::FPDF_BORDER_ON);
        $pdf->Cell(130, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, number_format($this->grandTotal, 2), self::FPDF_BORDER_ON);

        //$pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        //$pdf->Cell(60, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "y=" . $currYPos, 1);

        /*
        create key table
        1 x 4
        190 : 14:34 | 14:34 | 14:33 | 14:33
        */
        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(190, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * 2, "Colour Codes", self::FPDF_BORDER_OFF);
        $pdf->SetFont('Arial','B',8);
        $pdf->getNextLine();

        $pdf->SetXY(FactoryX_PickList_Model_Pdf_Picklist::PAGE_MAR_L, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H * $pdf->getNextLine());

        $pdf->SetFillColor($this->red[0],$this->red[1],$this->red[2]);
        $pdf->Cell(14, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "red:",
            self::FPDF_BORDER_ON,
            self::FPDF_LN_RIGHT,
            self::FPDF_ALIGN_LEFT,
            self::FPDF_FILL_ON
        );
        $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
        $pdf->Cell(34, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "partial order", 1);

        // consolidated stock
        $pdf->SetFillColor($this->green[0],$this->green[1],$this->green[2]);
        $pdf->Cell(14, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "green:",
            self::FPDF_BORDER_ON,
            self::FPDF_LN_RIGHT,
            self::FPDF_ALIGN_LEFT,
            self::FPDF_FILL_ON
        );
        $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
        $pdf->Cell(34, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "consolidated stock", 1);

        // pre-order stock
        $pdf->SetFillColor($this->blue[0],$this->blue[1],$this->blue[2]);
        $pdf->Cell(14, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "blue:",
            self::FPDF_BORDER_ON,
            self::FPDF_LN_RIGHT,
            self::FPDF_ALIGN_LEFT,
            self::FPDF_FILL_ON
        );
        $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
        $pdf->Cell(33, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "pre-order stock", 1);

        // deleted product
        $pdf->SetFillColor($this->orange[0],$this->orange[1],$this->orange[2]);
        $pdf->Cell(14, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "orange:",
            self::FPDF_BORDER_ON,
            self::FPDF_LN_RIGHT,
            self::FPDF_ALIGN_LEFT,
            self::FPDF_FILL_ON
        );
        $pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
        $pdf->Cell(33, FactoryX_PickList_Model_Pdf_Picklist::PDF_LINE_H, "product deleted", 1);
    }

    /**
    generates a code39 barcode and writes to tmp file in system tmp dir

    @param string $code the code to encode
    @static
    @see Zend_Barcode::factory
     * @param $code
     * @throws Exception
     * @throws Zend_Barcode_Exception
     * @throws Zend_Exception
     * @return bool|string
*/
    private static function generateBarcode($code) {
        // reuirement
        $code = strtoupper($code);
        // start with a barcode
        $config = new Zend_Config(array(
            'barcode'        => 'code39',
            'barcodeParams'  => array('text' => $code),
            'renderer'       => 'image',
            'rendererParams' => array('imageType' => 'png')
        ));
        $barcodeImage = Zend_Barcode::factory($config)->draw();
        // tempnam does not allow a file extension
        $tmpFile = self::secureTmpname(".png", "barcode");
        //$this->helper->log(sprintf("%s->[%s]tmpFile=%s", __METHOD__, $code, $tmpFile));
        Imagepng($barcodeImage, $tmpFile);
        return $tmpFile;
    }

    /**
    remove slashes from sku
     * @param $sku
     * @return mixed|string
*/
    private static function cleanSku($sku) {
        $sku = strtoupper($sku);
        $chars = array("/", "\\"); // , "-");
        $sku = str_replace($chars, "", $sku);
        return $sku;
    }

    /**
    each website has different colour attributes
    */
    function getColourField() {
        $colourField = $this->defaultColourField;
        $baseUrl = Mage::app()->getStore()->getBaseUrl();
        //$this->helper->log(sprintf("%s->baseUrl=%s", __METHOD__, $baseUrl));
        if (preg_match("/webdev2/", $baseUrl)) {
            $colourField = $this->colourFields['fx'];
        }
        if (preg_match("/(alannah|ah\-)/", $baseUrl)) {
            $colourField = $this->colourFields['ah'];
        }
        if (preg_match("/(gorman|gm\-)/", $baseUrl)) {
            $colourField = $this->colourFields['go'];
        }
        if (preg_match("/(danger|df\-)/", $baseUrl)) {
            $colourField = $this->colourFields['df'];
        }
        if (preg_match("/(jack|jl\-)/", $baseUrl)) {
            $colourField = $this->colourFields['jl'];
        }
        if (preg_match("/(claude|cm\-)/", $baseUrl)) {
            $colourField = $this->colourFields['cm'];
        }
        //$this->helper->log(sprintf("%s->colourField=%s", __METHOD__, $colourField));
        return $colourField;
    }

    /**
    each products can have a different size and or colour
    relevant attributes normally contain size or colour

    @params Mage_Sales_Model_Order_Item
     * @param $item
     * @return array
    */
    function getItemValues($item) {
        //$this->helper->log(sprintf("%s->item=%s", __METHOD__, get_class($item)) );
        $values = array(
            "attrInfo"  => false,
            "colour"    => "",
            "size"      => ""
        );

        $options = unserialize($item['product_options']);
        //$this->helper->log(sprintf("options=%s", print_r($options, true)) );

        if (array_key_exists('attributes_info', $options)) {
            foreach($options['attributes_info'] as $attr) {
                //$this->helper->log(sprintf("attr=%s", print_r($attr, true)) );
                if (preg_match("/colour/i", $attr['label'])) {
                    $values['colour'] = $attr['value'];
                }
                elseif (preg_match("/size/i", $attr['label'])) {
                    $values['size'] = $attr['value'];
                }
                // Gift Voucher Value
                elseif (preg_match("/voucher/i", $attr['label'])) {
                    $values['size'] = $attr['value'];
                }
            }
        }
        else {
            $values['attrInfo'] = false;
        }
        //$this->helper->log(sprintf("%s->%s:values=%s", __METHOD__, $item->getSku(), print_r($values, true)));
        return $values;
    }

    /**
     * @param $product
     * @return string
     * @throws Mage_Core_Exception
     */
    function getBrandField($product) {
        $brand = "brand";
        $baseUrl = Mage::app()->getStore()->getBaseUrl();
        try {
            /*
            if (preg_match("/(webdev2|bincani)/", $baseUrl)) {
                $brandField = $this->brandFields['fx'];
                $brand = $product->getAttributeText($brandField);
            }
            */
            if (preg_match("/(alannah|ah\-)/", $baseUrl)) {
                $brand = "AH";
            }
            if (preg_match("/(gorman|gm\-)/", $baseUrl)) {
                $brand = "GO";
            }
            if (preg_match("/(danger|df\-)/", $baseUrl)) {
                $brandField = $this->brandFields['df'];
                $brand = $product->getAttributeText($brandField);
            }
            if (preg_match("/(jack|jl\-)/", $baseUrl)) {
                $brand = "JL";
            }
            if (preg_match("/(claude|cm\-)/", $baseUrl)) {
                $brand = "CM";
            }
            if (preg_match("/(lurv|lv\-)/", $baseUrl)) {
                $brand = "LV";
            }
        }
        catch(Exception $ex) {
            $this->helper->log(sprintf("%s->Error: %s", __METHOD__, $ex->getMessage()));
        }
        //$this->helper->log(sprintf("%s->brand=%s", __METHOD__, $brand));
        return $brand;
    }

    /**
    TODO: there is probably a much better way of getting the "Default Store View" label
    */
    protected function getColourViaSku($sku, $colourField = "colour_description") {
        if (strlen($sku) < 13) {
            return;
        }
        $colourCode = substr($sku, 10, 3);
        if (!is_numeric($colourCode)) {
            return;
        }
        
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode(
            Mage_Catalog_Model_Product::ENTITY,
            $colourField
        );

        $colourValue = $attribute->getSource()->getOptionId($colourCode);
        //$this->helper->log(sprintf("%s->colour[%s]=%s|%s", __METHOD__, $colourField, $colourCode, $colourValue));
        
        // get colour  by text
        //$colour = $attribute->getSource()->getOptionText($colourCode);

        $storeId = 1; // admin = 0, default = 1
        if (count($this->colourOptions) === 0) {
            //$this->helper->log(sprintf("%s->load '%s[%s]' options", __METHOD__, $colourField, $attribute->getId()) );
            $this->colourOptions = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->getId())->setStoreFilter($storeId)->toOptionArray();
        }
        //$this->helper->log(sprintf("%s->values: %s", __METHOD__, print_r($this->colourOptions, true)) );
        foreach($this->colourOptions as $i => $option) {
            if ($option['value'] == $colourValue) {
                $colour = $option['label'];
            }
        }
        //$this->helper->log(sprintf("%s->colour[%s]=%s|%s", __METHOD__, $colourField, $colourCode, $colour));
        return $colour;
    }

    /**
    date_from       date from
    date_to         date to
    source          m | e (no longer in use)
    state           order state (usually processing)
    status          order status (usually processing)
    filter_region           array of region ids
    filter_region_apply     include | exclude filter
    filter_cg               array of customer group ids
    filter_cg_apply         include | exclude filter
    filter_pt               array of product types
    filter_region_apply     include | exclude filter
    include_zero    include items with zero left to ship
    sort_by         sort by
     * @param array $config
     * @throws Exception
     * @throws Mage_Core_Exception
     * @return array
    */
    private function getOrders($config = array()) {

        //$this->helper->log(sprintf("%s->config=%s", __METHOD__, print_r($config, true)) );

        $filterRegion               = $config['filter_region'];
        $filterRegionApply          = $config['filter_region_apply'];
        $customerGroupFilter        = $config['filter_cg'];
        $customerGroupFilterApply   = $config['filter_cg_apply'];
        $productTypeFilter          = $config['filter_pt'];
        $productTypeFilterApply     = $config['filter_pt_apply'];

        // create filter description
        $this->filterDesc = "";
        if (is_array($filterRegion)) {
            $filteredRegions = array();
            $regions = Mage::getModel('directory/region_api')->items("AU");
            foreach($regions as $region) {
                //$this->helper->log(sprintf("%s->%s", __METHOD__, print_r($region, true)));
                if (in_array($region['region_id'], $filterRegion)) {
                    //$this->helper->log(sprintf("%s->%s", __METHOD__, print_r($region, true)));
                    $filteredRegions[] = $region['name'];
                }
            }
            if (count($filteredRegions)) {
                $tmp = rtrim(implode(',', $filteredRegions), ',');
                $this->filterDesc = sprintf("%s:%s", $filterRegionApply, $tmp);
            }
        }

        /*
        processing
        processing_part_shipped
        processing_part_shipped_nt
        processing_shipped_nt
        processing_stage2
        */
        $statusFilter = array('eq' => $config['status']);

        /*
        processing_partial = processing_part_shipped | processing_part_shipped_nt | processing_shipped_nt | processing_stage2
        */
        if (preg_match("/^processing$/", $config['status'])) {
            $statusFilter = array(
                array('in' =>
                    array(
                        'processing',
                        'processing_part_shipped',
                        'processing_part_shipped_nt',
                        'processing_shipped_nt',
                        'processing_stage2'
                    )
                )
            );
        }
        else if (preg_match("/^processing_partial$/", $config['status'])) {
            $statusFilter = array(
                array('in' =>
                    array(
                        'processing_part_shipped',
                        'processing_part_shipped_nt'
                    )
                )
            );
        }
        

        // use to test
        //->addFieldToFilter('increment_id', array('eq' => '100051013'))

        $_orderCollection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('state', array('eq' => $config['state']))
            ->addFieldToFilter('status', $statusFilter);

        $fromDate = Mage::getModel('core/date')->gmtDate(self::DATE_FORMAT, $config['date_from']);
        $toDate = Mage::getModel('core/date')->gmtDate(self::DATE_FORMAT, $config['date_to']);

        $addWhere = "";
        $addWhere .= sprintf("created_at >= '%s' AND created_at <= '%s'", $fromDate, $toDate);

        // alternate where condition
        //$addWhere .= "(created_at >= '" . $fromDate . "' AND created_at <= '" . $toDate . "') ";
        //$addWhere .= "OR (updated_at >= '" . $fromDate . "' AND updated_at <= '" . $toDate . "')";
        
        $_orderCollection->getSelect()->where($addWhere);
        if (!empty($config['sort_by'])) {
            //$this->helper->log(sprintf("%s->sortby=%s", __METHOD__, $config['sort_by']) );
             $direction = "DESC";
            // puts express on the top
            if (preg_match("/ship/", $config['sort_by'])) {
                $direction = "ASC";
            }
            $_orderCollection->addAttributeToSort($config['sort_by'], $direction)
                ->addAttributeToSort('created_at', "ASC");
            // setOrder is an alias
            // $_orderCollection->setOrder($config['sort_by'], $direction);
        }

        switch ($config['source']) {
            case 'ebay':
                //$this->helper->log('source=ebay');
                $_orderCollection->addFieldToFilter('shipping_description', Array('like'=>'%ebay%'));
                break;
            case 'magento':
                //$this->helper->log('source=magento');
                $_orderCollection->addFieldToFilter('shipping_description', Array('nlike'=>'%ebay%'));
                break;
        }
        $sql = $_orderCollection->getSelect();
        //$this->helper->log(sprintf("%s->sql=%s", __METHOD__, $sql));
        //$this->helper->log(sprintf("%s->orders=%s", __METHOD__, count($_orderCollection)));

        if (count($_orderCollection) == 0) {
            //throw new Exception(sprintf("no orders found between [%s - %s]<pre>%s</pre>\n", $fromDate, $toDate, wordwrap($sql, $width = 100)));
            throw new Exception(sprintf("no orders found between [%s - %s]", $fromDate, $toDate));
        }

        $this->totalOrderCnt = 0;
        $this->totalItemCnt = 0;
        $gross_profit = 0;    // not in use; calculation is wrong
        $this->totalDiscounts = 0;
        $this->grandTotal = 0;
        $this->shippingPaidTotal = 0;
        $rows1 = array();
        $imgHelper = Mage::helper('catalog/image');
        $defaultImage = sprintf("%s%s", Mage::getBaseDir('skin'), "/frontend/default/default/images/catalog/product/placeholder/small_image.jpg");

        foreach($_orderCollection as $o) {
            // Mage_Sales_Model_Order
            $order = Mage::getModel('sales/order')->load($o->getId());

            // check gift wrapped
            //$this->helper->log(sprintf("canDisplayGiftmessage=%s", $order->canDisplayGiftmessage() ) );
            $message = Mage::getModel('giftmessage/message');
            $giftMessageId = $order->getGiftMessageId();
            $giftWrap = null;
            if (!is_null($giftMessageId)) {
                $message->load((int)$giftMessageId);
                $giftSender = $message->getData('sender');
                $giftRecipient = $message->getData('recipient');
                $giftMessage = $message->getData('message');
                $giftWrap = array(
                    'sender'    => $message->getData('sender'),
                    'recipient' => $message->getData('recipient'),
                    'message'   => $message->getData('message')
                );
                //$this->helper->log(sprintf("message=", print_r($giftWrap, true)) );
            }

            // Mage_Sales_Model_Order_Address
            $address = $order->getShippingAddress();
            $shippingMethod = $order->getShippingMethod();
            //$this->helper->log(sprintf("%s->ShippingMethod=%s", __METHOD__, $shippingMethod));
            $shippingMethodDesc = $order->getShippingDescription();
            //$this->helper->log(sprintf("%s->ShippingMethod=%s", __METHOD__, $shippingMethod));

            /**
            apply region filter to order address

            ALL
            international (not AU)
            */
            if (is_array($filterRegion)) {
                if ($filterRegionApply == 'include') {
                    // include all countries other than oz
                    if (in_array("INT", $filterRegion)) {
                        // if it is australia then check regions
                        if (preg_match("/au/i", $address->getCountry()) && !in_array($address->getRegionId(), $filterRegion) ) {
                            continue;
                        }
                    }
                    // if it is not ALL then check regions
                    else if (!in_array("ALL", $filterRegion) && !in_array($address->getRegionId(), $filterRegion)) {
                        continue;
                    }
                }
                if ($filterRegionApply == 'exclude') {
                    // exclude regions
                    if (in_array($address->getRegionId(), $filterRegion)) {
                        continue;
                    }
                    // exclude all countries other than oz
                    if (in_array("INT", $filterRegion) && !preg_match("/au/i", $address->getCountry()) ) {
                        continue;
                    }
                }
            }

            // apply customer group filter
            $this->helper->log(sprintf("customerGroupFilter=%s:%s", $customerGroupFilterApply, print_r($customerGroupFilter, true)) );
            if (is_array($customerGroupFilter)) {
                $this->helper->log(sprintf("getCustomerGroupId: %s", $order->getCustomerGroupId()) );
                if ($customerGroupFilterApply == 'include') {
                    // if it is not ALL then check groups
                    if (!in_array("ALL", $customerGroupFilter) && !in_array($order->getCustomerGroupId(), $customerGroupFilter)) {
                    //if (!in_array($order->getCustomerGroupId(), $customerGroupFilter)) {
                        $this->helper->log(sprintf("SKIP") );
                        continue;
                    }
                }
                if ($customerGroupFilterApply == 'exclude') {
                    if (in_array($order->getCustomerGroupId(), $customerGroupFilter)) {
                        continue;
                    }
                }
            }

            // summary info
            $this->totalOrderCnt++;
            $this->totalDiscounts += $order->getDiscountAmount();
            $this->grandTotal += $order->getGrandTotal();
            //print_r($order);

            //$customer = Mage::getModel('customer/customer')->load($o->getCustomerId());
            //print_r($customer);

            //$this->helper->log("number: " . $order->getIncrementId());
            $items = $order->getAllItems();

            $itemcount = count($items);
            $shipping = 0;
            $this->shippingPaidTotal = 0;
            $discountCode = "";
            //$discountPrice = 0;
            $discountPercent = 0;
            $rows2 = array();
            $totalWeight = 0;

            $sku = "";
            $hasParent = 0;
            $ignoreItem = false;
            $bundleCnt = 0;
            $parentType = "";
            $index = 0;
            $parentIndex = 0;
            $parentName = "";
            $isPartialOrder = false;
            $values = array();

            /**
            Mage_Sales_Model_Order_Item
            FactoryX_ShippedFrom_Model_Sales_Order_Item
            */
            foreach ($items as $itemId => $item) {

                /*
                $this->helper->log(sprintf("%s->%s|%s,%s,%s", __METHOD__,
                        $item->getSku(),
                        $item->isDummy(),
                        count($item->getChildrenItems()),
                        $item->getRealProductType()
                    )
                );
                */

                // over write previous (parent) with childs SKU
                if ($item->getParentItem() || $item->getParentItemId() ) {
                    $hasParent = 1;
                    //continue;
                }
                else {
                    $bundleCnt = 0;
                    // only attempt on configurables
                    $values = $this->getItemValues($item);
                    $colour = $values['colour'];
                    // get colour anotehr way
                    if (!$colour) {
                        $colour = $this->getColourViaSku($item->getSku());
                    }
                    $size = $values['size'];
                }

                $sku = $item->getSku();
                $productId = $item->getProductId();

                // Mage_Sales_Model_Order
                $orderItem = Mage::getModel('sales/order')->load($item->getOrderId());
                //print_r($orderItem);

                //$orderItem->getQtyOrdered();
                $shippingPaid = $orderItem->getShippingAmount() + $orderItem->getBaseShippingTaxAmount();

                // get item product (may no longer exist)
                try {
                    //$this->helper->log(sprintf("%s->%s:productId=%s", __METHOD__, $orderItem->getId(), $productId) );

                    $product = Mage::getModel('catalog/product')->load($productId);
                    //$this->helper->log(sprintf("%s->%s,type_id=%s,hasParent=%d", __METHOD__, $sku, $product->getData('type_id'), $hasParent));

                    if (!$hasParent) {
                        $parentType = $product->getData('type_id');
                    }

                    $parentIdArray = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                    // deprecated > 1.4.0
                    //$parentIdArray = $product->loadParentProductIds()->getData('parent_product_ids');

                    //$this->helper->log(sprintf("%s->parentIdArray=%s", __METHOD__, print_r($parentIdArray, true)));
                    $imgFile = "";

                    if (count($parentIdArray) >= 1) {
                        //$parent = Mage::getModel('catalog/product')->load($parentIdArray[0]);
                        //$this->helper->log(sprintf("%s->parent: %s", __METHOD__, get_class($parent)));

                        // new way - get the image
                        //$imgFile = $this->getProductImage($parentIdArray[0], $product->getAttributeText($this->getColourField()));

                        //$colourValue = $product->getAttributeText($this->getColourField());
                        //$this->helper->log(sprintf("%s->%s|%s|colourValue=%s,colour=%s", __METHOD__, get_class($product), $product->getData('type_id'), $colourValue, $colour) );
                        //$this->helper->log(sprintf("%s->getProductImage: %s, %s", __METHOD__, $parentIdArray[0], $colour) );
                        $imgFile = $this->getProductImage($parentIdArray[0], $colour);

                        //$imgFile = $this->getProductImage($parentIdArray[0], $product->getAttributeText($colour));

                        $weight = $this->getProductWeight($product->getId());
                        $totalWeight += $weight;
                        //$this->helper->log(sprintf("weight: %s", $weight) );
                    }
                    else {
                        $imgFile = $product->getImage(); //$imgHelper->init($product, 'image')->resize(100);
                        // no_selection ???
                        //$this->helper->log(sprintf("%s->getImage()=%s", __METHOD__, $imgFile));
                    }

                    //$this->helper->log(sprintf("%s->SKU:%s=imgFile:%s", __METHOD__, $sku, $imgFile));

                    if ($imgFile) {
                        $imgFile = Mage::getBaseDir('media') . "/catalog/product" . $imgFile;
                        //$this->helper->log("file_exists=" . $imgFile);
                        if (!file_exists($imgFile)) {
                            $imgFile = $defaultImage;
                        }
                    }
                    else {
                        $imgFile = $defaultImage;
                    }
                    //$this->helper->log(sprintf("%s->imgFile=%s", __METHOD__, $imgFile));
                }
                catch(Exception $e) {
                    $this->helper->log(__METHOD__ . " " . $e->getMessage());
                }

                //$customer = Mage::getModel('customer/customer')->load($o->getCustomerId());

                $orgSku = $sku;
                $sku = self::cleanSku($sku);
                //$this->helper->log("sku: " . $sku);

                //$discount = 0;
                $discountAmount = 0;
                $discountCode = $orderItem->getCouponCode();
                $discountPercent = $item->getDiscountPercent();

                $masterCoupon = Mage::getModel('salesrule/rule')->load($discountCode); // , 'coupon_code');
                if ($discountPercent > 0 && $masterCoupon && $masterCoupon->getId()) {
                    $discountAmount = $masterCoupon->getDiscountAmount();
                    //$discountPrice = $product->getSpecialPrice() * ($discountAmount / 100);
                    //$discount = $product->getSpecialPrice() - $discountPrice;
                }

                // can cause error Call to a member function getSource() on a non-object in Product.php
                //$colour = $product->getAttributeText($this->getColourField());

                $brand = $this->getBrandField($product);

                if ($hasParent) {
                    //$this->helper->log(sprintf("hasParent|ignoreItem=%d|%d", $hasParent,$ignoreItem));
                    $hasParent = 0;
                    if ($ignoreItem) {
                        $ignoreItem = false;
                        continue;
                    }
                    $tmpIndex = 0;
                    //$product->getData('type_id')
                    if ($parentType == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {

                        // next item in the bundle
                        if ($bundleCnt > 0) {
                            $index++;
                        }
                        // copy the parent
                        /*
                        if ($parentIndex != $index) {
                            $rows2[] = $rows2[$parentIndex - 1];
                        }
                        */
                        $tmpIndex = $index - 1;

                        $barCode = self::generateBarcode($sku);

                        // preserve the original name
                        $rows2[$tmpIndex]["name_parent"] = $parentName;
                        //$rows2[$tmpIndex]["online_only"] = $onlineOnly;
                        //$rows2[$tmpIndex]["name"] = sprintf("[%s] %s", $rows2[$parentIndex - 1]["name_parent"], $item->getName());
                        $rows2[$tmpIndex]["name"] = sprintf("%s", $item->getName());
                        $rows2[$tmpIndex]["barcode"] = $barCode;
                        $rows2[$tmpIndex]["sku"] = $sku;
                        $rows2[$tmpIndex]["image"] = $imgFile;

                        // bundle products use simple price
                        $taxAmount = $item->getTaxAmount() / $item->getQtyOrdered();

                        /**
                        TODO: check if tax is inclusive before making this calculation
                        */
                        //$gross_profit += (($item->getPrice() + $taxAmount) * $item->getQtyOrdered());
                        $gross_profit += $item->getBaseOriginalPrice() * $item->getQtyOrdered();

                        $rows2[$tmpIndex]["price1"]    = $item->getBaseOriginalPrice();
                        $rows2[$tmpIndex]["price2"]    = $item->getBasePrice();

                        if (empty($rows2[$tmpIndex]["colour"])) {
                            $rows2[$tmpIndex]["colour"] = $colour;
                        }
                        if (empty($rows2[$tmpIndex]["size"])) {
                            $rows2[$tmpIndex]["size"] = $size;
                        }
                        if (empty($rows2[$tmpIndex]["brand"])) {
                            $rows2[$tmpIndex]["brand"] = $brand;
                        }
                        $bundleCnt++;
                    }
                    else if ($parentType == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                        $tmpIndex = $index - 1;
                        $barCode = self::generateBarcode($sku);
                        $rows2[$tmpIndex]["barcode"] = $barCode;
                        $rows2[$tmpIndex]["sku"] = $sku;
                        $rows2[$tmpIndex]["image"] = $imgFile;
                        //$rows2[$tmpIndex]["online_only"] = $onlineOnly;
                        if (empty($rows2[$tmpIndex]["colour"])) {
                            $rows2[$tmpIndex]["colour"] = $colour;
                        }
                        if (empty($rows2[$tmpIndex]["size"])) {
                            $rows2[$tmpIndex]["size"] = $size;
                        }
                        if (empty($rows2[$tmpIndex]["brand"])) {
                            $rows2[$tmpIndex]["brand"] = $brand;
                        }
                    }
                    else {
                        // item has been deleted!
                        $tmpIndex = $index - 1;
                        $barCode = self::generateBarcode($sku);
                        $rows2[$tmpIndex]["deleted"] = true;
                        $rows2[$tmpIndex]["barcode"] = $barCode;
                        $rows2[$tmpIndex]["sku"] = $sku;
                        $rows2[$tmpIndex]["image"] = $imgFile;
                        if (empty($rows2[$tmpIndex]["colour"])) {
                            $rows2[$tmpIndex]["colour"] = $colour;
                        }
                        if (empty($rows2[$tmpIndex]["size"])) {
                            $rows2[$tmpIndex]["size"] = $size;
                        }
                        if (empty($rows2[$tmpIndex]["brand"])) {
                            $rows2[$tmpIndex]["brand"] = $brand;
                        }
                    }
                }
                else {
                    $this->totalItemCnt += $item->getQtyOrdered();

                    // apply product type filter
                    $this->helper->log(sprintf("%s->productTypeFilter=%s:%s", __METHOD__, $productTypeFilterApply, print_r($productTypeFilter, true)) );
                    if (is_array($productTypeFilter)) {

                        $this->helper->log(sprintf("%s->_getProductType: %s", __METHOD__, $this->_getProductType($item)) );

                        $this->helper->log(sprintf("%s->ORDER_ITEM_FIELD_CONSOLIDATED: %s", __METHOD__, $item->getData(self::ORDER_ITEM_FIELD_CONSOLIDATED)) );
                        $this->helper->log(sprintf("%s->ORDER_ITEM_FIELD_PREORDER: %s", __METHOD__, $item->getData(self::ORDER_ITEM_FIELD_PREORDER)) );


                        if ($productTypeFilterApply == 'include') {
                            // if it is not ALL then check groups
                            if (!in_array("ALL", $productTypeFilter) && !in_array($this->_getProductType($item), $productTypeFilter)) {
                                $ignoreItem = true;
                                $this->helper->log(sprintf("%s->EXCLUDE item '%s', skip", __METHOD__, $sku));
                                continue;
                            }
                        }
                        if ($productTypeFilterApply == 'exclude') {
                            if (in_array($this->_getProductType($item), $productTypeFilter)) {
                                $ignoreItem = true;
                                $this->helper->log(sprintf("%s->EXCLUDE item '%s', skip", __METHOD__, $sku));
                                continue;
                            }
                        }
                    }

                    /*
                    // check cons filter
                    if (!$config['include_cons'] && $item->getData(self::ORDER_ITEM_FIELD_CONSOLIDATED)) {
                        $ignoreItem = true;
                        $this->helper->log(sprintf("%s->consolidated item '%s', skip", __METHOD__, $sku));
                        continue;
                    }

                    // check pres filter
                    if (!$config['include_pres'] && $item->getData(self::ORDER_ITEM_FIELD_PREORDER)) {
                        $ignoreItem = true;
                        $this->helper->log(sprintf("%s->pre-order item '%s', skip", __METHOD__, $sku));
                        continue;
                    }
                    */

                    if (preg_match("/processing/", $config['status'])) {
                        $toShip = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyRefunded() - $item->getQtyCanceled();
                    }
                    else {
                        $toShip = $item->getQtyOrdered();
                    }
                    $this->helper->log(sprintf("%s->toShip[%s]=%d", __METHOD__, $config['status'], $toShip));
                    
                    if (!$config['include_zero'] && $toShip <= 0) {
                        $ignoreItem = true;
                        $this->helper->log(sprintf("%s->item toShip=%d '%s', skip", __METHOD__, $toShip, $sku));
                        continue;
                    }

                    // TO TEST: avoid dumping item and use fields e.g.
                    /*
                    foreach($this->itemAmounts as $amount) {
                        $this->helper->log(sprintf("%s->item[%s]: %s", __METHOD__, $amount, $item->getData($amount)) );
                    }
                    */

                    $barCode = self::generateBarcode($sku);

                    // tax is calculated based on the number of items, so needs to divided to get per item
                    $taxAmount = $item->getTaxAmount() / $item->getQtyOrdered();
                    /**
                    TODO: check if tax is inclusive before making this calculation
                    */
                    //$gross_profit += (($item->getPrice() + $taxAmount) * $item->getQtyOrdered());
                    $gross_profit += $item->getBaseOriginalPrice() * $item->getQtyOrdered();

                    $rows2[] = array(
                        "barcode"    => $barCode,
                        "sku"        => $sku,
                        "image"      => $imgFile,
                        "name"       => $item->getName(),
                        "online"     => $item->getData(self::ORDER_ITEM_FIELD_CONSOLIDATED),
                        "preorder"   => $item->getData(self::ORDER_ITEM_FIELD_PREORDER),
                        "stock"      => Mage::getModel('cataloginventory/stock_item')->loadByProduct(
                            Mage::getModel('catalog/product')->loadByAttribute('sku', $sku)
                        )->getQty(),

                        // base_price || base_original_price
                        "price1"    => $item->getBaseOriginalPrice(),
                        "price2"    => $item->getBasePrice(),

                        // price && original_price will return alt currency
                        //"price1"    => $item->getOriginalPrice(),
                        //"price2"    => $item->getPrice() + $taxAmount,

                        "tax"        => $taxAmount,
                        "discount"    => $discountAmount,
                        "cost"        => $product->getCost(),
                        "qty"        => $item->getQtyOrdered(),
                        "toShip"    => $toShip,
                        "colour"    => $colour,
                        "size"      => $size,
                        "brand"        => $brand,
                    );
                    $index++;
                    $parentIndex = $index;
                    $parentName = $item->getName();
                    if (!$isPartialOrder) {
                        if ($item->getQtyOrdered() != $toShip) {
                            //$this->helper->log(sprintf("%s->isPartialOrder:(%s != %s)", __METHOD__, $item->getQtyOrdered(), $toShip));
                            $isPartialOrder = true;
                        }
                    }

                }
            }

            // if no items in order then skip the rest
            if (count($rows2) == 0) {
                $this->helper->log(sprintf("%s->order '%s' has no items to show", __METHOD__, $order->getIncrementId()) );
                continue;
            }

            //$this->helper->log(sprintf("%s->rows=%s", __METHOD__, print_r($rows2, true)) );

            // adding shipping options to each order
            //$this->helper->log(sprintf("%s->shippingMethod=%s", __METHOD__, $shippingMethod) );
            /**
            fx_exp
            df_exp
            fx_exp_free
            fx_std
            fx_int
            fx_int_free
            fx_nz
            fx_nz_free
            */
            $shippingLabel = "";
            $shippingContainer = "bag";
            $accpacShippingKey = "";
            $calcWeight = true;
            switch ($shippingMethod) {
                case "flatrate_flatrate":
                    $shippingLabel = "express";
                    $accpacShippingKey = "fx_exp";
                    //$siteName = Mage::app()->getWebsite()->getName();
                    //$siteName = Mage::app()->getStore()->getFrontendName();
                    $baseUrl = Mage::app()->getStore()->getBaseUrl();
                    //$this->helper->log(sprintf("%s->baseUrl=%s", __METHOD__, $baseUrl) );
                    if ($shippingPaid == 0) {
                        $shippingLabel = sprintf("free %s", $shippingLabel);
                        $accpacShippingKey = "fx_exp_free";
                    }
                    elseif (preg_match("/danger/i", $baseUrl) ) {
                        $shippingLabel = sprintf("%s df", $shippingLabel);
                        $accpacShippingKey = "df_exp";
                    }
                    break;
                case "freeshipping_freeshipping":
                    $shippingLabel = "parcel";
                    $accpacShippingKey = "fx_std";
                    break;
                case "tablerate_bestway":
                    $shippingLabel = "international";

                    $accpacShippingKey = "fx_int";
                    if ($shippingPaid == 0) {
                        $shippingLabel = sprintf("free %s", $shippingLabel);
                        $accpacShippingKey = "fx_int_free";
                    }

                    // nz bro
                    if (preg_match("/nz/i", $address->getCountry()) ) {
                        $accpacShippingKey = "fx_nz";
                        if ($shippingPaid == 0) {
                            $shippingLabel = sprintf("free %s", $shippingLabel);
                            $accpacShippingKey = "fx_nz_free";
                        }
                    }
                    break;

                // name change
                case "eparcel_eparcel_parcel_510_days":
                case "eparcel_eparcel_standard_510_days":
                    $shippingContainer = "box";
                    $shippingLabel = "eparcel standard";
                    $accpacShippingKey = "fx_eparcel_std";
                    $calcWeight = false;
                    break;

                case "eparcel_eparcel_express_35_days":
                    $shippingContainer = "box";
                    $shippingLabel = "eparcel express";
                    $accpacShippingKey = "fx_eparcel_exp";
                    $calcWeight = false;
                    break;

                default:
                    //die(sprintf("Error: no accpac shipping codes for '%s'", $shippingMethod));
                    $shippingContainer = "box";
                    $shippingLabel = "temando";
                    $accpacShippingKey = "temando";
                    $calcWeight = false;
            }
            if (!array_key_exists($accpacShippingKey, $this->accpacShippingCodes)) {
                die(sprintf("Error: no accpac shipping codes for method & key '%s->%s'", $shippingMethod, $accpacShippingKey));
            }

            $this->shippingPaidTotal += $shippingPaid;
            $codes = $this->accpacShippingCodes[$accpacShippingKey];

            /**
            add shipping barcodes to order
            */
            foreach($codes as $postageSize => $code) {
                if ($calcWeight) {
                    $weight = ((int)$postageSize/1000);
                }
                else {
                    $weight = $totalWeight;
                }

                //$this->helper->log(sprintf("%s->code=%s", __METHOD__, $code));
                $barCode = self::generateBarcode($code);
                $rows2[] = array(
                    "barcode"     => $barCode,
                    "sku"        => $code,
                    "image"        => null,
                    "name"        => sprintf("%s post %s%0.1fKg %s", $shippingLabel, (($calcWeight) ? "" : "~"), $weight, $shippingContainer),
                    "discount"    => 0,
                    "colour"    => "",
                    "brand"        => "shipping",
                    "qty"        => 1,
                    "toShip"    => 1
                );
            }

            // add order
            $fullAddress = "";
            /*
            if ($address->getCompany()) {
                $this->helper->log("Company=" . $address->getCompany());
                $fullAddress = $address->getCompany() . ":";
            }
            */
            if ($address) {
                $name = $address->getName();
                $company = $address->getCompany();
                $phone = $address->getTelephone();
                $fullAddress .= sprintf("%s, %s, %s, %s, %s", $address->getStreetFull(), $address->getCity(), $address->getRegion(), $address->getPostcode(), $address->getCountry());
                if (strlen($fullAddress) > 90) {
                    $fullAddress = substr($fullAddress, 0, 90);
                    $fullAddress .= "...";
                }
            }
            else {
                $name = $order->getCustomerFirstname() . " " . $order->getCustomerLastname();
                $company = "";
                $phone = "";
            }
            $rows1[] = array(
                "number"     => $order->getIncrementId(),
                "status"     => $order->getStatus(),
                "created_at" => $order->getCreatedAt(),
                "updated_at" => $order->getUpdatedAt(),
                "source"     => $order->getShippingDescription(),
                "name"       => $name,
                "email"      => $order->getCustomerEmail(),
                "company"    => $company,
                "address"    => $fullAddress,
                "method"     => $shippingMethodDesc,
                "wrap"       => $giftWrap,
                "phone"      => $phone,
                "subtotal"   => $order->getSubtotal(),
                "discount"   => $order->getDiscountAmount(),
                "code"       => $discountCode,
                "shipping"   => $order->getShippingAmount(),
                "tax"        => $order->getTaxAmount(),
                "total"      => $order->getGrandTotal(),
                "partial"    => $isPartialOrder,
                "items"      => $rows2
            );
        } // end foreach order

        // check again, to allow for item level filtering
        if (count($rows1) == 0) {
            throw new Exception(sprintf("no orders found between [%s - %s] due to item level filtering", $fromDate, $toDate));
        }
        return $rows1;
    }

    /**
     * getProductImage
     *
     * search for the correct front image with the label front_colour
     * if not found OR no labels provided then use the first image (normally the front)
     * if no colour passed then use the first front image
     *
     * @param int $product_id
     * @param string $colour
     * @return string path to product image
    */
    function getProductImage($product_id, $colour = false) {
        $_product = Mage::getModel('catalog/product')->load($product_id);
        $images = $_product->getMediaGalleryImages();
        $productImage = "";
        $firstImage = "";
        $found = 0;
        foreach ($images as $image) {
            if (empty($firstImage)) {
                $firstImage = $image->getFile();
            }
            // if image has a label && a colour is passed
            $label = trim($image['label']);
            if (!empty($label) && $colour) {
                $label = trim($image['label']);
                // split label e.g. black_front
                $aLabel = explode('_', $label);
                $identifier = $aLabel[0];
                $labelColour = str_replace("-", " ", $aLabel[1]);
                // front picture and the colours match
                //$this->helper->log(sprintf("%s->id=%s (%s==%s)", __METHOD__, $identifier, $labelColour, $colour) );
                if ($identifier == 'front') {
                    // no colour || colour match
                    if (
                        !$colour
                        ||
                        ($colour && preg_match(sprintf("/%s/i", $labelColour), $colour))
                    ) {
                        //$this->helper->log(sprintf("FOUND: %s", $colour) );
                        $productImage = $image->getFile();
                        $found = 1;
                        break;
                    }
                }
            }
            // no labels then just use the first image
            else {
                $productImage = $image->getFile();
                $found = 1;
                break;
            }
        }
        // nothing found then just use the first image
        if (!$found) {
            $productImage = $firstImage;
        }
        return $productImage;
    }


    /**
     * @param $product_id
     * @return mixed
     */
    function getProductWeight($product_id) {
        $collection = Mage::getResourceModel('catalog/product_collection')
                    ->addFieldToFilter('entity_id', array($product_id))
                    ->addAttributeToSelect(array($this->defaultWeightField))
                    ->setPageSize(1);

        $_product = $collection->getFirstItem();
        //$this->helper->log(sprintf("product_id: %s, %s", $product_id, $this->defaultWeightField) );
        $weight = $_product->getData($this->defaultWeightField);
        //$weight = $_product->getWeight();
        return $weight;
    }

    /**
     * getProductType
     *
     * @param $item order item
     */
    private function _getProductType($item) {
        $pt = null;
        if ($item->getData(self::ORDER_ITEM_FIELD_CONSOLIDATED)) {
            $pt = FactoryX_PickList_Model_System_Config_Source_ProductTypes::PRODUCT_TYPE_CONSOLIDATED;
        }
        elseif ($item->getData(self::ORDER_ITEM_FIELD_PREORDER)) {
            $pt = FactoryX_PickList_Model_System_Config_Source_ProductTypes::PRODUCT_TYPE_PREORDER;
        }
        else {
            $pt = FactoryX_PickList_Model_System_Config_Source_ProductTypes::PRODUCT_TYPE_INSTORE;
        }

        // check type is defined
        $types = FactoryX_PickList_Model_System_Config_Source_ProductTypes::getProductTypes();
        if (!array_key_exists($pt, $types)) {
            throw new Exception(sprintf("unknown type '%s'!", $pt));
        }

        return $pt;
    }

    /**
    Example. secureTmpname(".pdf", "pickSheet")

    @param string   $postfix postfix to file (used for file extensions)
    @param string   $prefix prefix to file (used for naming)
    @param string   $dir path to output dir, defaults to system tmp
     * @param string $postfix
     * @param string $prefix
     * @param null $dir
     * @return bool|string
    */
    private static function secureTmpname($postfix = '.tmp', $prefix = 'tmp', $dir = null) {
        // validate arguments
        if (! (isset($postfix) && is_string($postfix))) {
            return false;
        }
        if (! (isset($prefix) && is_string($prefix))) {
            return false;
        }

        if (! isset($dir)) {
            $dir = sys_get_temp_dir();
        }
        if (! isset($dir)) {
            $dir = getcwd();
        }

        // find a temporary name
        $tries = 1;
        do {
            // get a known, unique temporary file name
            $sysFileName = tempnam($dir, $prefix);
            if ($sysFileName === false) {
                return false;
            }

            // tack on the extension
            $newFileName = $sysFileName . $postfix;
            if ($sysFileName == $newFileName) {
                return $sysFileName;
            }

            // move or point the created temporary file to the new filename
            // NOTE: these fail if the new file name exist
            //$newFileCreated = (isWindows() ? @rename($sysFileName, $newFileName) : @link($sysFileName, $newFileName));
            $newFileCreated = @link($sysFileName, $newFileName);
            if ($newFileCreated) {
                return $newFileName;
            }

            unlink ($sysFileName);
            $tries++;
        } while ($tries <= 5);
        return false;
    }

    /**
     * getItemName
     *
     * generates a full item name, including attributes
     *
     * @param object $item the order item
     * @return string the item name
     */
    public function getItemName($item) {
        //$this->helper->log(sprintf("%s->item=%s", __METHOD__, print_r($item, true)) );

        $append = false;
        $name = "";
        $itemName = "?";
        if (array_key_exists('name', $item) && !empty($item['name'])) {
            //$this->helper->log(sprintf("%s->name=%s", __METHOD__, $item['name']) );
            $itemName = $item['name'];
            $name = $itemName;
        }
        elseif (array_key_exists('name_parent', $item) && !empty($item['name_parent'])) {
            //$this->helper->log(sprintf("%s->name_parent=%s", __METHOD__, $item['name_parent']) );
            $itemName = $item['name_parent'];
            $name = $itemName;
        }
        if (array_key_exists('colour', $item) && !empty($item['colour'])) {
            //$this->helper->log(sprintf("%s->colour=%s", __METHOD__, $item['colour']) );
            $name = sprintf("%s - %s", $itemName, $item['colour']);
            $append = true;
        }
        // append size at the end
        if (array_key_exists('size', $item) && !empty($item['size'])) {
            //$this->helper->log(sprintf("%s->size=%s", __METHOD__, $item['size']) );
            if ($append) {
                $name = sprintf("%s - %s", $name, $item['size']);
            }
            else {
                $name = sprintf("%s - %s", $itemName, $item['size']);
            }
            $append = true;
        }
        // and brand at front
        if (array_key_exists('brand', $item) && !empty($item['brand']) && $item['brand'] != "shipping") {
            $brand = strtolower($item['brand']);
            if (
                array_key_exists($brand, $this->brandShow)
                &&
                $this->brandShow[$brand]
            ) {
                //$this->helper->log(sprintf("%s->brand=%s", __METHOD__, $item['brand']) );
                if ($append) {
                    $name = sprintf("%s - %s", $item['brand'], $name);
                }
                else {
                    $name = sprintf("%s - %s", $item['brand'], $itemName);
                }
            }
        }
        //$this->helper->log(sprintf("%s->return name: %s", __METHOD__, $name) );
        return $name;
    }

}