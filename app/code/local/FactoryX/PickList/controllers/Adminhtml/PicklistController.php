<?php
/*

Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);

route
/picklist/adminhtml_picklist/generatePicklist
*/

/**
 * Class FactoryX_PickList_Adminhtml_PicklistController
 */
class FactoryX_PickList_Adminhtml_PicklistController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/picklist');
    }

    /*
    */
    public function indexAction() {

        Mage::helper('picklist')->log(sprintf("%s", __METHOD__));

        // get current layout state
        //$this->loadLayout();
        $this->loadLayout()->_setActiveMenu('factoryx_menu/picklist');

        /**
        handles = route + controller + action
        e.g.
        admin_picklist|adminhtml_picklist|index
        */
        $handles = Mage::getSingleton('core/layout')->getUpdate()->getHandles();
        //Mage::helper('picklist')->log(sprintf("%s->handles=%s", __METHOD__, print_r($handles, true)) );

        /**
        load template
        */
        /*
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/template')->setTemplate('factoryx/picklist/generate.phtml')
        );
        */
        //OR
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'factoryx_picklist',
            array(
                'template' => 'factoryx/picklist/generate.phtml'
            )
        );
        $this->_addContent($block);

        //OR
        //$this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();

        // note. this will override the layout used above
        //$this->_initAction()->renderLayout();
    }

    /**
     set active menu
     */
    protected function _initAction() {

        Mage::helper('picklist')->log(sprintf("%s", __METHOD__));
        //$this->loadLayout()->_setActiveMenu('factoryx_menu/picklist');

        return $this;
    }

    /**
    */
    public function testBarcodeAction() {

        $mode = "png";
        $encoding = "128";
        $scale = 2;
        $font_loc = "/usr/share/php-barcode-0.3pl1/arialbd.ttf";

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

    /**

    */
    public function generatePicklistAction() {
        
         //Fetch submited params
        $params = $this->getRequest()->getParams();
        //Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, var_export($params, true)));

        $outFiles = array();
        $response = "";
        try {            
            if (!$params || !$params['report_from'] || !$params['report_to']) {
                throw new Exception("Please enter a valid 'from' and 'to' date.");
            }

            $tsFrom = Mage::helper('picklist/date')->convertDateToTs($params['report_from']);
            // make to the end of the day e.g. 23:59:59
            $tsTo = Mage::helper('picklist/date')->convertDateToTs($params['report_to']) + (3600 * 24 - 1);
            
            if ($tsFrom > $tsTo) {
                throw new Exception("Please enter a valid 'from' and 'to' date.");
            }
            $sortby = "date";
            if (isset($params['sort_by'])) {
                //Mage::helper('picklist')->log(sprintf("%s->sort_by=%s", __METHOD__, $params['sort_by']);
                $sortby = $params['sort_by'];
            }

            /*
            Mage::helper('picklist')->log(sprintf("%s->fromDate=%s", __METHD__, $fromDate));
            Mage::helper('picklist')->log(sprintf("%s->toDate=%s", __METHD__, $toDate));
            */

            $includeZero = false;
            if (isset($params['include_zero'])) {
                //Mage::helper('picklist')->log(sprintf("%s->include_zero=%s", __METHOD__, $params['include_zero']);
                $includeZero = true;
            }

            $includeImage = false;
            if (isset($params['include_image'])) {
                //Mage::helper('picklist')->log(sprintf("%s->include_image=%s", __METHOD__, $params['include_image']);
                $includeImage = true;
            }

            $includeSummary = false;
            if (isset($params['include_summary'])) {
                //Mage::helper('picklist')->log(sprintf("%s->include_summary=%s", __METHOD__, $params['include_summary']);
                $includeSummary = true;
            }

            $emailSent = false;
            $sendEmail = false;
            if (isset($params['send_email'])) {
                //Mage::helper('picklist')->log(sprintf("%s->send_email=%s", __METHOD__, $params['send_email']);
                $sendEmail = true;
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
            $regionFilterApply = "include"; // include | exclude
            if (isset($params['region_filter_apply'])) {
                $regionFilterApply = $params['region_filter_apply'];
            }

            $customerGroupFilter = "ALL";
            if (isset($params['customer_groups_filter'])) {
                $customerGroupFilter = $params['customer_groups_filter'];
            }
            $customerGroupFilterApply = "include";
            if (isset($params['customer_groups_filter_apply'])) {
                $customerGroupFilterApply = $params['customer_groups_filter_apply'];
            }

            $productTypeFilter = "ALL";
            if (isset($params['product_type_filter'])) {
                $productTypeFilter = $params['product_type_filter'];
            }
            $productTypeFilterApply = "include";
            if (isset($params['customer_groups_filter_apply'])) {
                $productTypeFilterApply = $params['product_type_filter_apply'];
            }

            $numberPdf = 1;
            if (isset($params['split_pdf']) && isset($params['send_to_stores']) && is_array($params['send_to_stores'])) {
                //Mage::helper('picklist')->log(sprintf("send_to_stores=%s", print_r($params['send_to_stores'], true)) );
                $numberPdf = count($params['send_to_stores']);
            }

            $picklist = Mage::getModel('picklist/picklist');
            $outFiles = $picklist->generate(
                array(
                    'order_from'        => $tsFrom,
                    'order_to'          => $tsTo,
                    'order_source'      => $orderSource,
                    'order_state'       => $state,
                    'order_status'      => $status,
                    'include_image'     => $includeImage,
                    'include_summary'   => $includeSummary,
                    'include_zero'      => $includeZero,
                    'sort_by'           => $sortby,
                    'filter_region'         => $regionFilter,
                    'filter_region_apply'   => $regionFilterApply,
                    'filter_cg'             => $customerGroupFilter,
                    'filter_cg_apply'       => $customerGroupFilterApply,
                    'filter_pt'             => $productTypeFilter,
                    'filter_pt_apply'       => $productTypeFilterApply,
                    'number_pdf'        => $numberPdf,
                    'file_output'       => $params['file_output']
                )
            );
            if (empty($outFiles)) {
                throw new Exception("no files found!");
            }

            //Mage::helper('picklist')->log(sprintf("outFiles=%s", print_r($outFiles, true)) );

            // Mage::getSingleton('core/session')->addSuccess('Ok ' . $pdf);
            foreach($outFiles as $file) {
                if (!file_exists($file)) {
                    throw new Exception(sprintf("failed to create file %s.", $file));
                }
            }
        }
        catch(Exception $ex) {
            $showError = true;
            Mage::helper('picklist')->log(sprintf("%s->Error: %s", __METHOD__, $ex->getMessage()) );
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            //Mage::getSingleton('core/session')->addError($ex->getMessage());
            $response = $ex->getMessage();
        }

        // output file/files
        if (!empty($showError) && $showError) {
            $this->loadLayout();
            /*
            $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template',
                'factoryx_picklist',
                array(
                    'template' => 'factoryx/picklist/error.phtml'
                )
            );            
            $this->_addContent($block);
            */
            $this->renderLayout();
        }
        else if (preg_match("/view/i", $params['file_output'])) {
            $output = Mage::getSingleton('picklist/output_view');
            $output->setFiles($outFiles);
            Mage::helper('picklist')->logPickListJob(
                'create',
                'form',
                //Mage::helper('core/http')->getRemoteAddr(true),
                json_encode($params),
                $params['file_output'],
                0,
                print_r($outFiles, true)
            );
            //$block = $this->getLayout()->createBlock('picklist/adminhtml_output_view');
            $block = new FactoryX_PickList_Block_Adminhtml_Output_View();
            //Mage::helper('picklist')->log(sprintf("%s->block=%s", __METHOD__, get_class($block)) );
            $this->_initAction()->_addContent($block)->renderLayout();
        }
        else if (preg_match("/download/i", $params['file_output'])) {
            $output = Mage::getSingleton('picklist/output_download');
            //$output = Mage::getModel("picklist/output_download");
            $store = "admin";
            if (array_key_exists('send_to_stores', $params)) {
                $stores = $params['send_to_stores'];
                if (is_array($stores)) {
                    shuffle($stores);
                }
                elseif(!empty($stores)) {
                    $store = $stores;
                }
            }

            if ($outFiles && count($outFiles)) {
                $outputPath = $output->createTmpDir();
                $response = substr($outputPath, strrpos($outputPath, "media"));
                $files = $output->copyFilesToMedia($outputPath, $outFiles, $stores, $store);
                $output->setFiles($files);
            }
            $output->setParams($params);

            $this->loadLayout();
            $block = $this->getLayout()->createBlock('FactoryX_PickList_Block_Adminhtml_Output_Download');

            // send emails
            if ($sendEmail) {
                $vars = array('from' => $tsFrom, 'to' => $tsTo);
                Mage::helper('picklist')->sendEmails($files, $vars, $params['additional_emails']);                
                $emailSent = true;
            }
            Mage::helper('picklist')->logPickListJob(
                'create',
                'form',
                //Mage::helper('core/http')->getRemoteAddr(true),
                json_encode($params),
                $params['file_output'],
                ($emailSent ? 1 : 0),
                $response
            );

            //Mage::helper('picklist')->log(sprintf("%s->block=%s", __METHOD__, get_class($block)) );
            //$this->getLayout()->getBlock('content')->append($block);
            $this->_addContent($block);
            $this->renderLayout();
        }
        else if (preg_match("/ftp/i", $params['file_output'])) {
            /*
            header('Cache-Control: public');
            header('Content-Type: text/html');
            echo "currently not implemented";
            exit();
            */
        }
        else {
            // no output
        }

        // redirect back to index action of (this) controller
        //$this->_redirect('example/Example1/');
    }


}
