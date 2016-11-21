<?php
/**

Mage_Core_Block_Template
Mage_Catalog_Block_Product_Abstract
Mage_Adminhtml_Block_Widget_Grid_Container
Mage_Adminhtml_Block_Widget_Form_Container
*/

class FactoryX_PickList_Block_Adminhtml_Output_View extends Mage_Core_Block_Template {

    /**
     * block constructor
     */
    public function __construct() {
        parent::__construct();

        //Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, print_r($this->getRequest()->getParams(), true)) );
        //$this->setTemplate('factoryx/picklist/output/view.phtml');

        $this->_blockGroup = 'picklist';
        $this->_controller = 'adminhtml_picklist';

        $output = Mage::getSingleton('picklist/output_view');
        $outFiles = $output->getFiles();
        //Mage::helper('picklist')->log(sprintf("%s->var=%s", __METHOD__, print_r($outFiles, true)) );
        
        // only support a single file
        $file = "";
        if (is_array($outFiles)) {
            if (count($outFiles) > 0) {
                $file = array_pop($outFiles);
            }
        }
        else if (!empty($outFiles)) {
            $file = $outFiles;
        }
        
        if (empty($file) || !is_file($file)) {
            echo sprintf("cannot find file '%s'", $file);
        }
        else {
            Mage::helper('picklist')->log(sprintf("file=%s", $file));
            header('Cache-Control: public'); // needed for i.e.
            header('Content-Type: application/pdf');
            // to initiate a download with a file name
            //header('Content-Disposition: attachment; filename="sample.pdf"');
            readfile($file);
        }
    }

}
