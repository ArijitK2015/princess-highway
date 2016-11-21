<?php

class FactoryX_ReportsViewer_Model_Report extends Varien_Object
{
    /**
     * Load report file info
     *
     * @param string fileName
     * @param string filePath
     * @return FactoryX_ReportsViewer_Model_Report
     */
    public function load($fileName, $filePath)
    {
        $reportData = Mage::helper('reportsviewer')->extractDataFromFile($filePath . DS . $fileName);

        $this->addData(array(
            'report_id'   => $fileName,
            'error' => $reportData->getError(),
            'url' => $reportData->getUrl(),
            'script_name' => $reportData->getScriptName(),
            'skin' => $reportData->getSkin(),
            'trace' => $reportData->getTrace(),
            'file' => $filePath . DS . $fileName,
            'added' => new Zend_Date((int)$reportData->getTime(), Mage::app()->getLocale()->getLocaleCode())
        ));

        return $this;
    }
}