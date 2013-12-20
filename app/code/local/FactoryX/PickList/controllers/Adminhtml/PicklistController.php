<?php
/*
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
*/

require_once("PickList.php");
require_once("PickListPDF.php");
require_once('php-barcode-0.3pl1/php-barcode.php');

class FactoryX_PickList_Adminhtml_PicklistController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		//Mage::helper('picklist')->log(sprintf("%s", __METHOD__));
		
		// get current layout state
		$this->loadLayout();
		$this->_setActiveMenu('factoryx_menu');
		
		/*
		Create core block based on factoryx/picklist/generate.phtml template (view) file
		Note the location "adminhtml"... needs to be there since this is admin controller
		*/
		
		/*
		$block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template',
			'factoryx_picklist',
			array(
				'template' => 'factoryx/picklist/generate.phtml'
			)
		);
		$this->getLayout()->getBlock('content')->append($block);

		//$this->_addContent($this->getLayout()->createBlock('picklist/adminhtml_picklist'));
		*/

		//$this->getLayout()->getBlock('right')->insert($block, 'catalog.compare.sidebar', true);
 		
		//Below example does the same thing, and looks cooler :)
		//$this->_addContent($block);
		
		$this->renderLayout();
	}

	public function testBarcodeAction() {
		$mode = "png";
		$encoding = "128";
		$scale = 2;
		
		$font_loc="/usr/share/php-barcode-0.3pl1/arialbd.ttf";
		$genbarcode_loc="/usr/local/bin/genbarcode";
		
		$bars = barcode_encode("test", $encoding);
		//print_r($bars);
		
		//$filename = "/tmp/test.png";
		//Mage::helper('picklist')->log("filename=" . $filename);

		/*		
		Mage::helper('picklist')->log("text=" . $bars['text']);
		Mage::helper('picklist')->log("bars=" . $bars['bars']);
		*/
		
		// Imagepng(barcode_outimage($bars['text'],$bars['bars'], $scale, $mode), $filename);
		header("Content-type: image/png");
		imagepng(barcode_outimage($bars['text'],$bars['bars'], $scale, $mode));
	}

	public function testPngAction() {
		$total_x = 120;
		$total_y = 50;
		$im=imagecreate($total_x, $total_y)
			or die("Cannot Initialize new GD image stream");

		$background_color = imagecolorallocate($im, 255, 255, 255);
		$text_color = imagecolorallocate($im, 233, 14, 91);
		imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);
		
		$green = imagecolorallocate($im, 132, 135, 28);
		imagerectangle($im, 0, 0, 119, 49, $green);
		
		header("Content-type: image/png");
		imagepng($im);
		imagedestroy($im);
	}

	public function generatePicklistAction() {
		 //Fetch submited params
        $params = $this->getRequest()->getParams();
		//Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, var_export($params, true)));
                
		$pdfOutFile = "";
        try {
        	/*
        	$timezone = Mage::app()->getStore($store)->getConfig("general/locale/timezone");
        	date_default_timezone_set($timezone);
        	Mage::helper('picklist')->log(__METHOD__ . "->timezone=" . $timezone);
        	*/
			if (!$params || !$params['report_from'] || !$params['report_to']) {
				throw new Exception("Please enter a valid 'from' and 'to' date.");
			}
			// convert to US format and set to AEST
			$parts = explode("/", $params['report_from']);
			$ts_from = mktime(0, 0, 0, $parts[1], $parts[0], $parts[2]);
			$parts = explode("/", $params['report_to']);
			$ts_to = mktime(0, 0, 0, $parts[1], $parts[0], $parts[2]) + (3600 * 24 - 1);
			
			if ($ts_from > $ts_to) {
				throw new Exception("Please enter a valid 'from' and 'to' date.");
			}
			
			$sortby = "date";
			if (isset($params['sort_by'])) {
				//Mage::helper('picklist')->log(sprintf("%s->sort_by=%s", __METHOD__, $params['sort_by']);				
				$sortby = $params['sort_by'];
			}			
			
			/*
			Mage::helper('picklist')->log("generatePicklistAction.fromDate=" . $fromDate);				
			Mage::helper('picklist')->log("generatePicklistAction.toDate=" . $toDate);
			*/
			$includeImage = false;
			if (isset($params['include_image'])) {
				//Mage::helper('picklist')->log(sprintf("%s->include_image=%s", __METHOD__, $params['include_image']);				
				$includeImage = true;
			}

			$state = "processing";
			$status = "processing";
			if (isset($params['status'])) {
				$status = $params['status'];
				if ($status == "complete") {
					$state = "complete";
				}
				//Mage::helper('picklist')->log(sprintf("%s->status=%s", __METHOD__, $status));
			}			
			
			$orderSource = "m";
			if (isset($params['order_source'])) {
				$orderSource = $params['order_source'];
				//Mage::helper('picklist')->log(sprintf("%s->order_source=%s", __METHOD__, $orderSource));
			}
			
        	$regionFilter = "ALL";
        	if (isset($params['region_filter'])) {
        		$regionFilter = $params['region_filter'];
        	}

        	// include | exclude
        	$applyFilter = "include";
        	if (isset($params['apply_filter'])) {
        		$applyFilter = $params['apply_filter'];
        	}
			
			$picklist = new PickList();
			$pdfOutFile = $picklist->generate(
				array(
					'order_from'	=> $ts_from,
					'order_to'		=> $ts_to,
					'order_source'  => $orderSource,
					'order_state'	=> $state,
					'order_status'	=> $status,
					'include_image' => $includeImage,
					'sort_by'       => $sortby,
					'filter_region'	=> $regionFilter,
					'region_apply'	=> $applyFilter,
				)
			);
			// Mage::getSingleton('core/session')->addSuccess('Ok ' . $pdf);
			
			if (!file_exists($pdfOutFile)) {
				throw new Exception("Failed to create pdf.");
			}
        }        
        catch(Exception $ex) {
			Mage::helper('picklist')->log(__METHOD__ . "Error: " . $ex->getMessage());
			echo "Error: " . $ex->getMessage();
            //Mage::getSingleton('core/session')->addError($ex->getMessage());
		}
		
		if (file_exists($pdfOutFile)) {
			//Mage::helper('picklist')->log("pdfOutFile=" . $pdfOutFile);
			header('Cache-Control: public'); // needed for i.e.
            header('Content-Type: application/pdf');
            //header('Content-Disposition: attachment; filename="sample.pdf"');
            readfile($pdfOutFile);
            exit();
		}
		
        // redirect back to index action of (this) controller		
		//$this->_redirect('example/Example1/');
	}

}
