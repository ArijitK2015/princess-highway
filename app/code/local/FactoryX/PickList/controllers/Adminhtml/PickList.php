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

require_once("PickListPDF.php");
require_once('php-barcode-0.3pl1/php-barcode.php');
//require_once('/usr/share/php/php-barcode-0.3pl1/encode_bars.php');
$font_loc = "arialbd.ttf";

class PickList {
	
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
    */   
    protected $accpacShippingCodes = array(
        // flatrate_flatrate
        "fx_exp" => array(
            "5000" => "zz22225000",
            "3000" => "zz22223000",
            "0500" => "zz22220500"
        ),
        "df_exp" => array(
            "5000" => "zzd2225000",
            "3000" => "zzd2223000",
            "0500" => "zzd2220500"
        ),
        "fx_exp_free" => array(
            "5000" => "zz22200003",
            "3000" => "zz22200002",
            "0500" => "zz22200001"
        ),
        // freeshipping_freeshipping 
        "fx_std"  => array(
            "5000" => "zz22205000",
            "3000" => "zz22203000",
            "0500" => "zz22200500"
        ),
        // tablerate_bestway
        "fx_int" => array(
            "5000" => "zz22220000"
        ),
        "fx_int_free" => array(
            "5000" => "zz22200000"
        ),
        // eparcel
        "fx_eparcel_std"  => array(
            "0" => "ep22205000",
        ),
        "fx_eparcel_exp"  => array(
            "0" => "ep22206000",
        )
    );

	protected $white = array(255,255,255);
	protected $black = array(0,0,0);	
	protected $red = array(255,0,0);
	protected $orange = array(255,188,159);
	protected $grey = array(240,240,240);

	protected $defaultColourField = "colour";
	protected $defaultSizeField = "size";
	protected $defaultWeightField = "weight";
	
	protected $colourFields = array(
		"ah" => "colour_all",
		"go" => "colour",
		"jl" => "colour_description",
		"df" => "colour_description",
		"fx" => "colour",
		"cm" => "color"
	);

	protected $brandFields = array(
		"fx" => "manufacturer",
		"df" => "brand"
	);
	
    public function __construct() {
    	//Mage::helper('picklist')->log("construct()");
    }
	
	/*
	times are entered as locale times
	*/
	public function generate($config) {
		//Mage::helper('picklist')->log(sprintf("%s->%s", __METHOD__, print_r($config, true)));
        $sortby = "";

		$ts_from = $config['order_from'];
		$ts_to = $config['order_to'];
		$source = $config['order_source'];
		$state = $config['order_state'];
		$status = $config['order_status'];
		$includeImage = $config['include_image'];
		if (array_key_exists('sort_by', $config)) {
		    $sortby = $config['sort_by'];
		}
		
		$helper = new FactoryX_PickList_Helper_Data();
		
		$fromDisplay = date(self::DATE_FORMAT, $ts_from);
		$toDisplay = date(self::DATE_FORMAT, intval($ts_to));

		// catch date error wierdness 1970
		if (preg_match("/1970/", $fromDisplay) || preg_match("/1970/", $toDisplay)) {
			throw new Exception("date exception [$fromDisplay - $toDisplay]\n");
		}
				
		// get Mage directory
		//Mage::helper('picklist')->log("fp1:" . dirname( __FILE__ ));
		//Mage::helper('picklist')->log("fp2:" . dirname($_SERVER['PHP_SELF']));
		//Mage::helper('picklist')->log("fp3:" . $_SERVER['DOCUMENT_ROOT']);
		
		$barcode_dir = $_SERVER['DOCUMENT_ROOT'] . "/var/barcode";
		if (!file_exists($barcode_dir)) {
			//Mage::helper('picklist')->log("mkdir:" . $barcode_dir);
			mkdir($barcode_dir, 0777);
		}
		$pdf_dir = $_SERVER['DOCUMENT_ROOT'] . "/var/pdf";
		if (!file_exists($pdf_dir)) {
			//Mage::helper('picklist')->log("mkdir:" . $pdf_dir);
			mkdir($pdf_dir, 0777);
		}
/*		
		ini_set('auto_detect_line_endings', true);
		$auto_detect_line_endings = ini_get('auto_detect_line_endings');
		Mage::helper('picklist')->log("auto_detect_line_endings=" . $auto_detect_line_endings);
*/
        $filterDesc = "";
        $order_cnt = 0;
        $item_cnt = 0;
        $shippingPaidTotal = 0;
        $grand_total = 0;
        $total_discounts = 0;
        $rows1 = $this->getOrders(
            array(
        		'date_from'     => $config['order_from'],
        		'date_to'       => $config['order_to'],
        		'source'        => $config['order_source'],
        		'state'         => $config['order_state'],
        		'status'        => $config['order_status'],
		        'filter_region' => $config['filter_region'],
		        'region_apply'  => $config['region_apply'],
        		'sort_by'       => $sortby,
        		'barcode_dir'   => $barcode_dir
            ),
            $filterDesc,
            $order_cnt,
            $item_cnt,
            $shippingPaidTotal,
            $grand_total,
            $total_discounts
        );

		setlocale(LC_TIME, "en_AU");
		$pdf_filename = $pdf_dir . "/picklist_" . strftime("%Y%m%d_%H%M%S", time()) . ".pdf";
		
		$pdf = new PickListPDF();
		$pdf->SetAutoPageBreak($auto = true, $bottomMargin = 10);
		
		$pdf->setFromDate($fromDisplay);
		$pdf->setToDate($toDisplay);
		$pdf->setStatus($status);
		$pdf->setFilter($filterDesc);
				
		$pdf->AliasNbPages();
		$pdf->AddNewPage();
		$pdf->SetFont('Arial','',8);
		
		//$pdf->Cell(40,10,'');
		foreach ($rows1 as $row) {
		    //Mage::helper('picklist')->log(sprintf("%s->partial=%s", __METHOD__, $row['partial']));			
			$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine());

			$source = "m";
			//Mage::helper('picklist')->log(sprintf("%s->source=%s,%s", __METHOD__, $row["source"], stripos($row["source"],"ebay"))) );
			if (stripos($row["source"],"ebay") !== false) {
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
            $pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine(0.1, false));
            //Mage::helper('picklist')->log(sprintf("%s->ypos=%s", __METHOD__, $ypos) );

            $pdf->SetLineWidth(0.2);
			$pdf->Cell(22, PickListPDF::PDF_LINE_H, sprintf("#%s", $row["number"]),
			    self::FPDF_BORDER_ON,
			    self::FPDF_LN_RIGHT,
			    self::FPDF_ALIGN_LEFT,
			    self::FPDF_FILL_ON
            );
			
			$pdf->SetDrawColor(0,0,0);
			$pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
			
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(48, PickListPDF::PDF_LINE_H, $row["name"], self::FPDF_BORDER_ON);
			$pdf->SetFont('Arial','',8);
			
			$width = 120;
			//if ($row["company"]) {
            $pdf->Cell(60, PickListPDF::PDF_LINE_H, $row["company"], self::FPDF_BORDER_ON);
			$pdf->Cell(60, PickListPDF::PDF_LINE_H, substr($row["method"], 0, 40), self::FPDF_BORDER_ON);
			$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
			
			$pdf->Cell(70, PickListPDF::PDF_LINE_H, sprintf("E:%s T:%s", $row["email"], $row["phone"]), self::FPDF_BORDER_ON);
			$pdf->Cell(120, PickListPDF::PDF_LINE_H, $row["address"], self::FPDF_BORDER_ON);
            $pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine(1, false, "name"));

			$itemCnt = 0;
			$addedShipingHeader = false;
			foreach ($row["items"] as $item) {
				$itemCnt++;
				if ($includeImage) {
					$pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
				}
				else {
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
				}

                // start rendering item
                $pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine());

				//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
				/**
				Start shipping section
				**/
				if ($item['brand'] == "shipping" && !$addedShipingHeader) {
				    $addedShipingHeader = true;
				    //$pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
				    //$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine());
				    //$pdf->Cell(0, 8, "*** POST BAGS USED ***", 1, 2, 'C', true);
				    //$pdf->Line(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine(), PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine());
				    //$pdf->Rect(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine(), 190, 1);
				    $pdf->dashedLine(0.6);
				    $pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine(0.1, false));
				}
				
                // start with a barcode
				$pdf->Image($item['barcode'], PickListPDF::PAGE_MAR_L,  PickListPDF::PDF_LINE_H * $pdf->getCurrentLine(), self::IMG_BARCODE_W, self::IMG_BARCODE_H);
				$pdf->SetXY(PickListPDF::PAGE_MAR_L + self::IMG_BARCODE_W, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine());
				
				$append = false;
				$name = $item['name'];
				if (strlen($item['colour'])) {
				    $append = true;
					$name = sprintf("%s - %s", $item['name'], $item['colour']);
				}
				// append size
				if (array_key_exists('size', $item) && !empty($item['size'])) {
				    if ($append) {
				        $name = sprintf("%s - %s", $name, $item['size']);
				    }
				    else {
					    $name = sprintf("%s - %s", $item['name'], $item['size']);
					}
				}
				
				// not sure about this ???
				if (array_key_exists('name_org', $item) && strlen($item['name_org'])) {
					$name = sprintf("%s - %s", $item['name_org'], $name);
				}
				elseif (array_key_exists('brand', $item) && strlen($item['brand']) && $item['brand'] != "shipping") {
					$name = sprintf("%s - %s", $item['brand'], $name);
				}
                
				$outLine = false;
				$toShip = (array_key_exists('toShip', $item))?$item['toShip']:0;
                if (array_key_exists("partial", $row) && $row["partial"]) {
                    //$pdf->SetDrawColor($this->red[0],$this->red[1],$this->red[2]);
    				// $status... !preg_match("/ship/i", $name)
    				// processing_partial = processing_part_shipped|processing_part_shipped_nt|processing_shipped_nt|processing_stage2
                    $partialStatus = "processing_partial|processing_stage2";
                    //Mage::helper('picklist')->log(sprintf("%s->status=%s,%s", __METHOD__, $status, $partialStatus) );
                    //preg_match(sprintf("/%s/i", $partialStatus), $status) && 
    				if ($toShip > 0 && !preg_match("/ship/i", $name)) {
    					$outLine = true;
    				}
                }

                // layout shipping item differently								
				if ($item['brand'] == "shipping") {
				    $pdf->Cell(130, PickListPDF::PDF_LINE_H, $name, ($outLine?"LT":self::FPDF_BORDER_OFF), 0, 'L', true);
				    $pdf->SetFont('Arial','B', 10);
				    /*
				    $pdf->Cell(8, PickListPDF::PDF_LINE_H, "", ($outLine?"T":self::FPDF_BORDER_OFF), 0, 'L', true);
    				$pdf->SetFont('Arial','B', 8);
	    			$pdf->Cell(82, PickListPDF::PDF_LINE_H, "", ($outLine?"TR":self::FPDF_BORDER_OFF), 0, 'L', true);		    
	    			*/
				}
				else {
				    $pdf->Cell(40, PickListPDF::PDF_LINE_H, $item['sku'], ($outLine?"LT":self::FPDF_BORDER_OFF), 0, 'L', true);
				    $pdf->SetFont('Arial','B', 10);
				    $pdf->Cell(8, PickListPDF::PDF_LINE_H, number_format($toShip, 0), ($outLine?"T":self::FPDF_BORDER_OFF), 0, 'L', true);
    				$pdf->SetFont('Arial','B', 8);
    				$pdf->Cell(82, PickListPDF::PDF_LINE_H, $name, ($outLine?"TR":self::FPDF_BORDER_OFF), 0, 'L', true);				    
				}
				
				$pdf->SetFont('Arial','', 8);
				
				if ($item['brand'] == "shipping") {
                    $pdf->SetXY(70, PickListPDF::PDF_LINE_H * $pdf->getNextLine(1, false));
                    // EMPTY CELL
                    $x = PickListPDF::PAGE_MAX_W - (self::IMG_BARCODE_W + 10);
    				$pdf->Cell($x, PickListPDF::PDF_LINE_H, "", ($outLine?"LB":self::FPDF_BORDER_OFF), 0, 'L', true);
				}
				else {
				    /**
				    PRICE LINE
				    **/
                    $pdf->SetXY(70, PickListPDF::PDF_LINE_H * $pdf->getNextLine(1, false, $name));
                    
                    // 1st section:  price & tax e.g. $20.50 (0.00)
                    $tax = (array_key_exists('tax', $item))?$item['tax']:0;
    				$price = (array_key_exists('price1', $item))?$item['price1']:0;
    				$tPrice = sprintf("$%0.2f (%0.2f)", number_format($price, 2), number_format($tax, 2));
    				$pdf->Cell(48, PickListPDF::PDF_LINE_H, $tPrice, ($outLine?"LB":self::FPDF_BORDER_OFF), 0, 'L', true);
    				
    				// 2nd section:  price e.g. $20.50
    				$price = (array_key_exists('price1', $item))?$item['price2']:0;
    				$tPrice = sprintf("$%0.2f", number_format($price, 2));
      				$pdf->Cell(52, PickListPDF::PDF_LINE_H, $tPrice, ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);
    				
    				// 3rd section of price line
    				if (array_key_exists('discount', $item) && $item['discount'] && $item['discount'] > 0) {
    				    $tPrice = sprintf("%d%%", number_format($item['discount'], 0));
    					$pdf->Cell(30, PickListPDF::PDF_LINE_H, $tPrice, ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);
    				}
    				else {
    				    // EMPTY CELL
    					$pdf->Cell(30, PickListPDF::PDF_LINE_H, "", ($outLine?"B":self::FPDF_BORDER_OFF), 0, 'L', true);
    				}
                }

				$pdf->SetDrawColor(0,0,0);
				
			    /**
			    DRAW 3 OR 1 BLANK BOX(ES) - for ticking
			    **/
				//$pdf->SetXY($pdf->GetX() - 5, $pdf->GetY());
				$pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
				if ($item['brand'] == "shipping") {
                    $pdf->Rect(185, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine(), PickListPDF::BOX_W, PickListPDF::BOX_W, "DF");
                    $pdf->Rect(190, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine(), PickListPDF::BOX_W, PickListPDF::BOX_W, "DF");
			    }
			    
			    if ($toShip > 1) {
                    $startPos = 195;
                    $maxBoxes = 10;
                    $i = 0;
			        for(; ($i < $toShip && $i < $maxBoxes); $i++) {
                        $pdf->Rect($startPos - ($i * 5), PickListPDF::PDF_LINE_H * $pdf->getCurrentLine(), PickListPDF::BOX_W, PickListPDF::BOX_W, "DF");
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
		                $pdf->Cell(4, PickListPDF::PDF_LINE_H, "+", 0, 0, 'L', true);
		                $pdf->SetFont('Arial','', 8);
		            }
			    }
			    else {
                    $pdf->Rect(195, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine(), PickListPDF::BOX_W, PickListPDF::BOX_W, "DF");
                }
                //$pdf->Cell(4, 4, " ", self::FPDF_BORDER_ON, 0, 'L', true);
				
				// image
				if ($includeImage && $item['brand'] != "shipping") {
					//Mage::helper('picklist')->log(sprintf("render:%s", $item['image']));
				    if ($item['image'] && is_file($item['image'])) {
    					$info = pathinfo($item['image']);
    					$pdf->Image($item['image'], 10, PickListPDF::PDF_LINE_H * $pdf->getNextLine(), 27, 30, $info['extension']);
    				}
    				/*
    				else {
    				    $pdf->Rect(10, PickListPDF::PDF_LINE_H * $pdf->getNextLine(), 27, 30, "D");
    				}
    				*/
    				$pdf->getNextLine(5);
				}
				$pdf->getNextLine();
			}
			
			$pdf->SetFillColor($this->white[0],$this->white[1],$this->white[2]);
			
			$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getCurrentLine());

			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(15, PickListPDF::PDF_LINE_H, "Subtotal:", self::FPDF_BORDER_ON);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(17, PickListPDF::PDF_LINE_H, sprintf("$%0.2f", number_format($row['subtotal'], 2)), self::FPDF_BORDER_ON);

			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(18, PickListPDF::PDF_LINE_H, "Shipping:", self::FPDF_BORDER_ON);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(15, PickListPDF::PDF_LINE_H, sprintf("$%0.2f", number_format($row['shipping'], 2)), self::FPDF_BORDER_ON);

			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(20, PickListPDF::PDF_LINE_H, "Discount:", self::FPDF_BORDER_ON);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(58, PickListPDF::PDF_LINE_H, '$' . number_format($row['discount'], 2) . " " . $row['code'], self::FPDF_BORDER_ON);
			$pdf->SetFont('Arial','B',8);
			
			$pdf->Cell(10, PickListPDF::PDF_LINE_H, "Tax:", self::FPDF_BORDER_ON);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(10, PickListPDF::PDF_LINE_H, '$' . number_format($row['tax'], 2), self::FPDF_BORDER_ON);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(10, PickListPDF::PDF_LINE_H, "Total:", self::FPDF_BORDER_ON);	
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(17, PickListPDF::PDF_LINE_H, '$' . number_format($row['total'], 2), self::FPDF_BORDER_ON);
		
		    $pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());		    
			$time = strtotime($row['created_at']);
			$created_at = strftime("%d-%m-%Y %H:%M:%S%p", Mage::getModel('core/date')->timestamp($time));
			$time = strtotime($row['updated_at']);
			$updated_at = strftime("%d-%m-%Y %H:%M:%S%p", Mage::getModel('core/date')->timestamp($time));
			
			$info = sprintf("Ordered on: %s, Last updated: %s, Status: %s", $created_at, $updated_at, $helper->getStatusLabel($row['status']));
			$pdf->Cell(190, PickListPDF::PDF_LINE_H, $info, self::FPDF_BORDER_ON);
			
			$pdf->getNextLine(1.5);
		}
		
		$pdf->getNextLine(3);
		$av_order = $item_cnt / $order_cnt;

		// create stats page
		$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(190, PickListPDF::PDF_LINE_H * 2, "Summary", self::FPDF_BORDER_OFF);
		$pdf->SetFont('Arial','B',8);
		$pdf->getNextLine();
		
		$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Number of orders:", self::FPDF_BORDER_ON);
		$pdf->Cell(150, PickListPDF::PDF_LINE_H, $order_cnt, 1);
		
		$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Number of items ordered:", self::FPDF_BORDER_ON);
		$pdf->Cell(150, PickListPDF::PDF_LINE_H, $item_cnt, 1);
		
		$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Av items per order:", self::FPDF_BORDER_ON);
		$pdf->Cell(150, PickListPDF::PDF_LINE_H, number_format($av_order, 2), self::FPDF_BORDER_ON);
		
		$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Gross Profit:", self::FPDF_BORDER_ON);
		$pdf->Cell(150, PickListPDF::PDF_LINE_H, number_format($grand_total - $shippingPaidTotal, 2), self::FPDF_BORDER_ON);

	    // gross
		//$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		//$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Gross1 (no discount):", self::FPDF_BORDER_ON);
		//$pdf->Cell(150, PickListPDF::PDF_LINE_H, number_format($gross_profit, 2), self::FPDF_BORDER_ON);

		//$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		//$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Gross2 (no discount):", self::FPDF_BORDER_ON);
		//$pdf->Cell(150, PickListPDF::PDF_LINE_H, number_format($grand_total - $shippingPaidTotal + ($total_discounts * -1), 2), self::FPDF_BORDER_ON);

		$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Gross Shipping:", self::FPDF_BORDER_ON);
		$pdf->Cell(150, PickListPDF::PDF_LINE_H, number_format($shippingPaidTotal, 2), self::FPDF_BORDER_ON);

		$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Total Discount:", self::FPDF_BORDER_ON);
		$pdf->Cell(150, PickListPDF::PDF_LINE_H, number_format($total_discounts, 2), self::FPDF_BORDER_ON);
		
		$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		$pdf->Cell(40, PickListPDF::PDF_LINE_H, "Total Gross (inc shipping):", self::FPDF_BORDER_ON);
		$pdf->Cell(150, PickListPDF::PDF_LINE_H, number_format($grand_total, 2), self::FPDF_BORDER_ON);

		//$pdf->SetXY(PickListPDF::PAGE_MAR_L, PickListPDF::PDF_LINE_H * $pdf->getNextLine());
		//$pdf->Cell(40, PickListPDF::PDF_LINE_H, "y=" . $currYPos, 1);
			
		$pdf->Output($pdf_filename, "F");
		return $pdf_filename;
	}

	function generate_barcode($code, $filename) {
		$mode = "png";
		$encoding = "128";
		$scale = 1;
		$bars = barcode_encode($code,$encoding);
		//print_r($bars);
		
		//Mage::helper('picklist')->log("filename=" . $filename);
		//Mage::helper('picklist')->log("text=" . $bars['text']);
		//Mage::helper('picklist')->log("bars=" . $bars['bars']);
	
		Imagepng(barcode_outimage($bars['text'],$bars['bars'], $scale, $mode), $filename);
	}
	
	function clean_sku($sku) {
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
		//Mage::helper('picklist')->log(sprintf("%s->baseUrl=%s", __METHOD__, $baseUrl));
		if (preg_match("/webdev2/", $baseUrl)) {
			$colourField = $this->colourFields['fx'];
		}
		if (preg_match("/alannah/", $baseUrl)) {
			$colourField = $this->colourFields['ah'];
		}								
		if (preg_match("/gorman/", $baseUrl)) {
			$colourField = $this->colourFields['go'];
		}						
		if (preg_match("/danger/", $baseUrl)) {
			$colourField = $this->colourFields['df'];
		}				
		if (preg_match("/jack/", $baseUrl)) {
			$colourField = $this->colourFields['jl'];
		}
		if (preg_match("/claude/", $baseUrl)) {
			$colourField = $this->colourFields['cm'];
		}
		//Mage::helper('picklist')->log(sprintf("%s->colourField=%s", __METHOD__, $colourField));
		return $colourField;
	}

	/**
	each products can have a different size and or colour
	relevant attributes normally contain size or colour
	
	@params Mage_Sales_Model_Order_Item
	*/
	function getItemValues($item) {
	    //Mage::helper('picklist')->log(sprintf("%s->item=%s", __METHOD__, get_class($item)) );
        $values = array(
            "attrInfo" => false,
            "colour" => "",
            "size"   => ""
        );
        
        $options = unserialize($item['product_options']);
        //Mage::helper('picklist')->log(sprintf("options=%s", print_r($options, true)) );
        
        if (array_key_exists('attributes_info', $options)) {
            foreach($options['attributes_info'] as $attr) {
                //Mage::helper('picklist')->log(sprintf("attr=%s", print_r($attr, true)) );
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
		//Mage::helper('picklist')->log(sprintf("%s->%s:values=%s", __METHOD__, $item->getSku(), print_r($values, true)));
		return $values;
	}

	function getBrandField($product) {	
		$brand = "unknown";
		$baseUrl = Mage::app()->getStore()->getBaseUrl(); 
		try {
    		if (preg_match("/(webdev2|bincani)/", $baseUrl)) {
    			$brandField = $this->brandFields['fx'];
    			$brand = $product->getAttributeText($brandField);
    		}
    		if (preg_match("/alannah/", $baseUrl)) {
    			$brand = "AH";
    		}								
    		if (preg_match("/gorman/", $baseUrl)) {
    			$brand = "GO";
    		}								
    		if (preg_match("/danger/", $baseUrl)) {
    			$brandField = $this->brandFields['df'];
    			$brand = $product->getAttributeText($brandField);
    		}				
    		if (preg_match("/jack/", $baseUrl)) {
    			$brand = "JL";
    		}
        }
        catch(Exception $ex) {
            Mage::helper('picklist')->log(sprintf("%s->Error: %s", __METHOD__, $ex->getMessage()));
        }
		//Mage::helper('picklist')->log(sprintf("%s->brand=%s", __METHOD__, $brand));
		return $brand;
	}
	
	
    private function getOrders($config = array(), &$filterDesc, &$order_cnt, &$item_cnt, &$shippingPaidTotal, &$grand_total, &$total_discounts) {

		$filterRegion = $config['filter_region'];
		$filterApply = $config['region_apply'];        

		// create filter descrption
		$filterDesc = "";
		if (is_array($filterRegion)) {
			$filteredRegions = array();
			$regions = Mage::getModel('directory/region_api')->items("AU");
			foreach($regions as $region) {
				//Mage::helper('picklist')->log(sprintf("%s->%s", __METHOD__, print_r($region, true)));
				if (in_array($region['region_id'], $filterRegion)) {
					//Mage::helper('picklist')->log(sprintf("%s->%s", __METHOD__, print_r($region, true)));
					$filteredRegions[] = $region['name'];
				}
			}
			if (count($filteredRegions)) {
				$tmp = rtrim(implode(',', $filteredRegions), ',');
				$filterDesc = sprintf("%s:%s", $filterApply, $tmp);
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
        // processing_partial = processing_part_shipped|processing_part_shipped_nt|processing_shipped_nt|processing_stage2;
        //if (preg_match("/processing_partial/", $status)) {
            $statusFilter = array(
                array('in' => array(
                    'processing',
                    'processing_part_shipped',
                    'processing_part_shipped_nt',
                    'processing_shipped_nt',
                    'processing_stage2')
                )
            );
        //}

		$_orderCollection = Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('state', array('eq' => $config['state']))
			->addFieldToFilter('status', $statusFilter);

        $fromDate = Mage::getModel('core/date')->gmtDate(self::DATE_FORMAT, $config['date_from']);
        $toDate = Mage::getModel('core/date')->gmtDate(self::DATE_FORMAT, $config['date_to']);

		$addWhere = "";
		$addWhere .= sprintf("created_at >= '%s' AND created_at <= '%s'", $fromDate, $toDate);
		//$addWhere .= "(created_at >= '" . $fromDate . "' AND created_at <= '" . $toDate . "') ";
		//$addWhere .= "OR (updated_at >= '" . $fromDate . "' AND updated_at <= '" . $toDate . "')";
		$_orderCollection->getSelect()->where($addWhere);
		if (!empty($config['sort_by'])) {
		    //Mage::helper('picklist')->log(sprintf("%s->sortby=%s", __METHOD__, $config['sort_by']) );
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
				//Mage::helper('picklist')->log('source=ebay');
				$_orderCollection->addFieldToFilter('shipping_description', Array('like'=>'%ebay%'));
				break;
			case 'magento':
				//Mage::helper('picklist')->log('source=magento');
				$_orderCollection->addFieldToFilter('shipping_description', Array('nlike'=>'%ebay%'));
				break;
		}
        $sql = $_orderCollection->getSelect();
		if (count($_orderCollection) == 0) {	
			throw new Exception(sprintf("no orders found between [%s - %s]<pre>%s</pre>\n", $fromDate, $toDate, $sql));
		}
		//Mage::helper('picklist')->log(sprintf("%s->sql=%s", __METHOD__, $sql));

		$order_cnt = 0;
		$item_cnt = 0;
		$gross_profit = 0;	// not in use; calculation is wrong
		$total_discounts = 0;
		$grand_total = 0;
		$shippingPaidTotal = 0;
		$rows1 = array();
		$imgHelper = Mage::helper('catalog/image');
		$defaultImage = Mage::getBaseDir('skin') . "/frontend/default/default/images/catalog/product/placeholder/small_image.jpg";
		
		foreach ( $_orderCollection as $o ) {
			// Mage_Sales_Model_Order
			$order = Mage::getModel('sales/order')->load($o->getId());
			// Mage_Sales_Model_Order_Address
			$address = $order->getShippingAddress();
			/*
			Mage::helper('picklist')->log(sprintf("%s->address.Name=%s", __METHOD__, $address->getName()));
			Mage::helper('picklist')->log(sprintf("%s->address.StreetFull=%s", __METHOD__, $address->getStreetFull()));
			Mage::helper('picklist')->log(sprintf("%s->address.City=%s", __METHOD__, $address->getCity()));
			Mage::helper('picklist')->log(sprintf("%s->address.Region=%s", __METHOD__, $address->getRegionId()));
			Mage::helper('picklist')->log(sprintf("%s->address.Postcode=%s", __METHOD__, $address->getPostcode()));
			Mage::helper('picklist')->log(sprintf("%s->address.Country=%s", __METHOD__, $address->getCountry()));
            Mage::helper('picklist')->log(sprintf("%s->address.Country=%s", __METHOD__, $address->getCountry()));
            */

			$shippingMethod = $order->getShippingMethod();
			//Mage::helper('picklist')->log(sprintf("%s->ShippingMethod=%s", __METHOD__, $shippingMethod));			
			$shippingMethodDesc = $order->getShippingDescription();
			//Mage::helper('picklist')->log(sprintf("%s->ShippingMethod=%s", __METHOD__, $shippingMethod));

			// apply region filter
			if (is_array($filterRegion)) {
				if ($filterApply == 'include') {
					if (!in_array("ALL", $filterRegion) && !in_array($address->getRegionId(), $filterRegion)) {
						continue;
					}
				}
				if ($filterApply == 'exclude' && in_array($address->getRegionId(), $filterRegion)) {
					continue;
				}
			}
			// summary info
			$order_cnt++;
			$total_discounts += $order->getDiscountAmount();
			$grand_total += $order->getGrandTotal();
			//print_r($order);
			
			//$customer = Mage::getModel('customer/customer')->load($o->getCustomerId());
			//print_r($customer);

			//Mage::helper('picklist')->log("number: " . $order->getIncrementId());			
			$items = $order->getAllItems();
			
			$itemcount = count($items);
			$shipping = 0;
			$shippingPaidTotal = 0;
			$discountCode = "";
			//$discountPrice = 0;
			$discountPercent = 0;
			$rows2 = array();
			$totalWeight = 0;
					
			$sku = "";		
			$hasParent = 0;
			$bundleCnt = 0;
			$parentType = "";
			$index = 0;
			$parentIndex = 0;
			$parentName = "";
            $isPartialOrder = false;		
            $values = array();
			foreach ($items as $itemId => $item) {				
				// over write previous (parent) with childs SKU
				if ($item->getParentItem()) {
					$hasParent = 1;
					//continue;
				}
				else {
					$bundleCnt = 0;
                    // only attempt on configurables
    				$values = $this->getItemValues($item);
    				$colour = $values['colour'];
    				$size = $values['size'];					
				}
				
				$sku = $item->getSku();
				$productId = $item->getProductId();
				
				// Mage_Sales_Model_Order
				$orderItem = Mage::getModel('sales/order')->load($item->getOrderId());
				//print_r($orderItem);

				//$orderItem->getQtyOrdered();
				$shippingPaid = $orderItem->getShippingAmount() + $orderItem->getBaseShippingTaxAmount();

				// get image file
				try {
                    //Mage::helper('picklist')->log(sprintf("%s->%s:productId=%s", __METHOD__, $orderItem->getId(), $productId) );
    				$product = Mage::getModel('catalog/product')->load($productId);
    				
    				//Mage::helper('picklist')->log(sprintf("%s->%s,type_id=%s,hasParent=%d", __METHOD__, $sku, $product->getData('type_id'), $hasParent));
    
    				if (!$hasParent) {
    					$parentType = $product->getData('type_id');
    					//Mage::helper('picklist')->log(sprintf("%s->type_id=%s,hasParent=%d", __METHOD__, $parentType, $hasParent));					
    				}
				    
					$parentIdArray = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
					// deprecated > 1.4.0
					//$parentIdArray = $product->loadParentProductIds()->getData('parent_product_ids');
					//Mage::helper('picklist')->log(sprintf("%s->parentIdArray=%s", __METHOD__, print_r($parentIdArray, true)));					
					
					if (count($parentIdArray) >= 1) {
						//$parent = Mage::getModel('catalog/product')->load($parentIdArray[0]);
						//Mage::helper('picklist')->log(sprintf("%s->parent: %s", __METHOD__, get_class($parent)));
						
						// new way - get the image
						//$imgFile = $this->getProductImage($parentIdArray[0], $product->getAttributeText($this->getColourField()));

						//$colourValue = $product->getAttributeText($this->getColourField());
						//Mage::helper('picklist')->log(sprintf("%s->%s|%s|colourValue=%s,colour=%s", __METHOD__, get_class($product), $product->getData('type_id'), $colourValue, $colour) );
						$imgFile = $this->getProductImage($parentIdArray[0], $colour);
						
						//Mage::helper('picklist')->log(sprintf("%s->getProductImage: %s, %s", __METHOD__, $parentIdArray[0], $colour) );
						//$imgFile = $this->getProductImage($parentIdArray[0], $product->getAttributeText($colour));
						
						$weight = $this->getProductWeight($product->getId());
						$totalWeight += $weight;
						//Mage::helper('picklist')->log(sprintf("weight: %s", $weight) );
					}
					else {
						$imgFile = $product->getImage(); //$imgHelper->init($product, 'image')->resize(100);
					}
					//Mage::helper('picklist')->log(sprintf("%s->%s=imgFile:%s", __METHOD__, $sku, $imgFile));
					if ($imgFile) {
						$imgFile = Mage::getBaseDir('media') . "/catalog/product" . $imgFile;					
						//Mage::helper('picklist')->log("file_exists=" . $imgFile);
						if (!file_exists($imgFile)) {
							$imgFile = $defaultImage;
						}
					}
					else {
						$imgFile = $defaultImage;
					}
					//Mage::helper('picklist')->log(sprintf("%s->imgFile=%s", __METHOD__, $imgFile));
				}
				catch(Exception $e) {
					Mage::helper('picklist')->log(__METHOD__ . " " . $e->getMessage());
				}
				
				//$customer = Mage::getModel('customer/customer')->load($o->getCustomerId());
				
				$orgSku = $sku;
				$sku = PickList::clean_sku($sku);
				//Mage::helper('picklist')->log("sku: " . $sku);
				
				//$discount = 0;
				$discountAmount = 0;
				$discountCode = $orderItem->getCouponCode();
				$discountPercent = $item->getDiscountPercent();
				//Mage::helper('picklist')->log($sku . "; discountCode=" . $discountCode . "," . $discountPercent);
				$masterCoupon = Mage::getModel('salesrule/rule')->load($discountCode); // , 'coupon_code');
				if ($discountPercent > 0 && $masterCoupon && $masterCoupon->getId()) {
					$discountAmount = $masterCoupon->getDiscountAmount();
					//$discountPrice = $product->getSpecialPrice() * ($discountAmount / 100);
					//$discount = $product->getSpecialPrice() - $discountPrice;
		        }
		        
		        //Mage::helper('picklist')->log("hasParent: " . $hasParent);
				// can cause error Call to a member function getSource() on a non-object in Product.php
				//$colour = $product->getAttributeText($this->getColourField());
				
				$brand = $this->getBrandField($product);
		        
				if ($hasParent) {
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
						$png_filename = sprintf("%s/barcode_%s_%s.png", $config['barcode_dir'], $sku, strftime("%Y%m%d_%H%M%S", time()));
						PickList::generate_barcode($sku, $png_filename);
						// preserve the original name
						$rows2[$tmpIndex]["name_org"] = $parentName;
						//$rows2[$tmpIndex]["name"] = sprintf("[%s] %s", $rows2[$parentIndex - 1]["name_org"], $item->getName());
						$rows2[$tmpIndex]["name"] = sprintf("%s", $item->getName());
						$rows2[$tmpIndex]["barcode"] = $png_filename;
						$rows2[$tmpIndex]["sku"] = $sku;
						$rows2[$tmpIndex]["image"] = $imgFile;

						// bundle products use simple price
						$taxAmount = $item->getTaxAmount() / $item->getQtyOrdered();
						$gross_profit += (($item->getPrice() + $taxAmount) * $item->getQtyOrdered());						
						$rows2[$tmpIndex]["price1"]	= $item->getOriginalPrice();
						$rows2[$tmpIndex]["price2"]	= $item->getPrice() + $taxAmount;

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
						$png_filename = sprintf("%s/barcode_%s_%s.png", $config['barcode_dir'], $sku, strftime("%Y%m%d_%H%M%S", time()));
						PickList::generate_barcode($sku, $png_filename);
						$rows2[$tmpIndex]["barcode"] = $png_filename;
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
					else {
						// item has been deleted!
						$tmpIndex = $index - 1;
						$png_filename = sprintf("%s/barcode_%s_%s.png", $config['barcode_dir'], $sku, strftime("%Y%m%d_%H%M%S", time()));
						PickList::generate_barcode($sku, $png_filename);
						$rows2[$tmpIndex]["deleted"] = true;
						$rows2[$tmpIndex]["barcode"] = $png_filename;
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
					$hasParent = 0;
				}
				else {
					$item_cnt += $item->getQtyOrdered();
					$ordered = $item->getQtyOrdered();
					//Mage::helper('picklist')->log(sprintf("%s->ordered:%s", __METHOD__, $ordered));
					$toShip = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyRefunded() - $item->getQtyCanceled();
					//Mage::helper('picklist')->log(sprintf("%s->toShip:%s", __METHOD__, $toShip));
					
					$png_filename = sprintf("%s/barcode_%s_%s.png", $config['barcode_dir'], $sku, strftime("%Y%m%d_%H%M%S", time()));
					//Mage::helper('picklist')->log("png_filename: " . $png_filename);
					PickList::generate_barcode($sku, $png_filename);
					$taxAmount = $item->getTaxAmount() / $item->getQtyOrdered();
					$gross_profit += (($item->getPrice() + $taxAmount) * $item->getQtyOrdered());
					$rows2[] = array(
						"barcode" 	=> $png_filename,
						"sku"		=> $sku,
						"image"		=> $imgFile,
						"name"		=> $item->getName(),
						"price1"	=> $item->getOriginalPrice(),
						"price2"	=> $item->getPrice() + $taxAmount,
						"tax"		=> $taxAmount,
						"discount"	=> $discountAmount,
						"cost"		=> $product->getCost(),
						"qty"		=> $item->getQtyOrdered(),
						"toShip"    => $toShip,
						"colour"	=> $colour,
						"size"      => $size,
						"brand"		=> $brand,
					);
					$index++;
					$parentIndex = $index;
					$parentName = $item->getName();
					if (!$isPartialOrder) {
					    if ($item->getQtyOrdered() != $toShip) {
                            //Mage::helper('picklist')->log(sprintf("%s->isPartialOrder:(%s != %s)", __METHOD__, $item->getQtyOrdered(), $toShip));
					        $isPartialOrder = true;
					    }
                    }
					
				}
			}
		
			// adding shipping options to each order
			//Mage::helper('picklist')->log(sprintf("%s->shippingMethod=%s", __METHOD__, $shippingMethod) );
            /**
            fx_exp
            df_exp
            fx_exp_free
            fx_std
            fx_int
            fx_int_free
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
                    //Mage::helper('picklist')->log(sprintf("%s->baseUrl=%s", __METHOD__, $baseUrl) );
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
                    die(sprintf("Error: no accpac shipping codes for '%s'", $shippingMethod));
            }
            if (!array_key_exists($accpacShippingKey, $this->accpacShippingCodes)) {
                die(sprintf("Error: no accpac shipping codes for method & key '%s->%s'", $shippingMethod, $accpacShippingKey));
            }
            
            $shippingPaidTotal += $shippingPaid;
            $codes = $this->accpacShippingCodes[$accpacShippingKey];
            
			foreach($codes as $postageSize => $code) {
			    if ($calcWeight) {
                    $weight = ((int)$postageSize/1000);
                }
                else {
                    $weight = $totalWeight;
                }
    			$png_filename = sprintf("%s/barcode_%s_%s.png", $config['barcode_dir'], $code, strftime("%Y%m%d_%H%M%S", time()) );
    			PickList::generate_barcode($code, $png_filename);
    			$rows2[] = array(
    				"barcode" 	=> $png_filename,
    				"sku"		=> $code,
    				"image"		=> null,
    				"name"		=> sprintf("%s post %s%0.1fKg %s", $shippingLabel, (($calcWeight) ? "" : "~"), $weight, $shippingContainer),
    				//"price1"	=> $order->getShippingAmount(),
    				//"price2"	=> $shipping,
    				//"tax"		=> $shipping - $order->getShippingAmount(),
    				"discount"	=> 0,
    				"colour"	=> "",
    				"brand"		=> "shipping",
    				"qty"		=> 1,
    				"toShip"	=> 1
    			);
    		}

			// add order
			$fullAddress = "";
			/*
			if ($address->getCompany()) {
				Mage::helper('picklist')->log("Company=" . $address->getCompany());
				$fullAddress = $address->getCompany() . ":";
			}
			*/
			$fullAddress .= sprintf("%s, %s, %s, %s, %s", $address->getStreetFull(), $address->getCity(), $address->getRegion(), $address->getPostcode(), $address->getCountry());
			
			if (strlen($fullAddress) > 90) {
                $fullAddress = substr($fullAddress, 0, 90);
                $fullAddress .= "...";
			}
			
			$rows1[] = array(
				"number"	=> $order->getIncrementId(),
				"status"	=> $order->getStatus(),
				"created_at"=> $order->getCreatedAt(),
				"updated_at"=> $order->getUpdatedAt(),
				"source"	=> $order->getShippingDescription(),
				"name"		=> $address->getName(),
				// "name"		=> $order->getCustomerFirstname() . " " . $order->getCustomerLastname(),
				"email"		=> $order->getCustomerEmail(),
				"company"	=> $address->getCompany(),
				"address"	=> $fullAddress,
				"method"	=> $shippingMethodDesc,
				"phone"		=> $address->getTelephone(),
				"subtotal"	=> $order->getSubtotal(),
				"discount"	=> $order->getDiscountAmount(),
				"code"		=> $discountCode,
				"shipping"	=> $order->getShippingAmount(),
				"tax"		=> $order->getTaxAmount(),
				"total"		=> $order->getGrandTotal(),
				"partial"   => $isPartialOrder,
				"items"		=> $rows2
			);
		}
		return $rows1;
	}        
	
	/**
	search for the correct front image with the label front_colour
	if not found OR no labels provided then use the first image (normally the front)
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
    		// if image has a label
    		$label = trim($image['label']);
    		if (!empty($label)) {
    			$label = trim($image['label']);
    			// split label e.g. black_front
    			$aLabel = explode('_', $label);
    			$identifier = $aLabel[0];
    			$labelColour = str_replace("-", " ", $aLabel[1]);
    			// front picture and the colours match
    			//Mage::helper('picklist')->log(sprintf("%s->id=%s (%s==%s)", __METHOD__, $identifier, $labelColour, $colour) );
    			if ($identifier == 'front' && preg_match(sprintf("/%s/i", $labelColour), $colour) ) {
    			    //Mage::helper('picklist')->log(sprintf("FOUND: %s", $colour) );
    				$productImage = $image->getFile();
    				$found = 1;
    				break;
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
	

	function getProductWeight($product_id) {
    	$collection = Mage::getResourceModel('catalog/product_collection')
			->addFieldToFilter('entity_id', array($product_id))
			->addAttributeToSelect(array($this->defaultWeightField))
			->setPageSize(1);
		
		$_product = $collection->getFirstItem();
    	//Mage::helper('picklist')->log(sprintf("product_id: %s, %s", $product_id, $this->defaultWeightField) );
        $weight = $_product->getData($this->defaultWeightField);
        //$weight = $_product->getWeight();
    	return $weight;
	}	
	
}

?>