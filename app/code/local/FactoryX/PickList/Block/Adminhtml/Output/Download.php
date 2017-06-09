<?php
/**

Mage_Core_Block_Template
Mage_Adminhtml_Block_Template
*/

class FactoryX_PickList_Block_Adminhtml_Output_Download extends Mage_Core_Block_Template {

    protected $subject;

    /**
     * block constructor
     */
    public function __construct() {
        
        parent::__construct();

        //Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, print_r($this->getRequest()->getParams(), true)) );
        $this->setTemplate('factoryx/picklist/output/download.phtml');

        //$this->_blockGroup = 'picklist';
        //$this->_controller = 'adminhtml_picklist/generatePicklist';
        
        $output = Mage::getSingleton('picklist/output_download');
        $outFiles = $output->getFiles();
        $params = $output->getParams();
        $this->setOutFiles($outFiles);
        //Mage::helper('picklist')->log(sprintf("%s->outFiles=%s", __METHOD__, print_r($outFiles, true)) );
        
        $fromTs = Mage::helper('picklist/date')->convertDateToTs($params['report_from']);
        $toTs = Mage::helper('picklist/date')->convertDateToTs($params['report_to']);
        $this->subject = Mage::helper('picklist')->getSubject($fromTs, $toTs);
        $this->setHeaderTitle($this->subject);

        /*
        $target = "target='_blank' rel='noopener noreferrer'";
        foreach ( $files as $file) {
            // direct link
            //$link1 = sprintf("<a href='%s' %s>%s</a><br/>", $file['url'], $target, $file['url']);
            $this->getDocumentLink( $file);
        }
        */
        
    }

    /**
     * link via controller
     * @param $file
     * @return string
     */
    public function getDocumentLink($file) {
        $storeId = Mage::helper('picklist')->getFrontEndStoreId();
        $getDoc = Mage::app()->getStore($storeId)->getUrl('picklist/index/get', array(
                '_nosid'    => true,
                '_secure'   => false,
                '_query'    => array(
                    'file_path' => $file['file'],
                    'store'     => $file['store']
                )
            )
        );
        if (Mage::helper('picklist')->isMinifyUrlsEnabled()) {
            $getDoc = Mage::helper('picklist')->minifyUrl($getDoc);
        }
        $link = sprintf("<a href='%s' target='_blank' rel='noopener noreferrer'>%s</a><br/>", $getDoc, $file['name']);
        return $link;
    }

}
