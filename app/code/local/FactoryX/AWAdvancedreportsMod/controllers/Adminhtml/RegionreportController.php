<?php

/**
 * Class FactoryX_AWAdvancedreportsMod_RegionController
 */
class FactoryX_AWAdvancedreportsMod_Adminhtml_RegionreportController extends AW_Advancedreports_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/advancedreports/region');
    }

    public function indexAction()
    {
        Mage::helper('advancedreports')->updatePrototypeJS();
        $this->loadLayout()
            ->_setActiveMenu('report/advancedreports/region')
            ->_setSetupTitle(Mage::helper('advancedreports')->__('Sales by Region'))
            ->_addBreadcrumb(Mage::helper('advancedreports')->__('Advanced'), Mage::helper('advancedreports')->__('Advanced'))
            ->_addBreadcrumb(Mage::helper('advancedreports')->__('Sales by Region'), Mage::helper('advancedreports')->__('Sales by Region'))
            ->_addContent($this->getLayout()->createBlock('awadvancedreportsmod/advanced_region'))
            ->renderLayout();
    }


    public function exportOrderedCsvAction()
    {
        $fileName = 'region.csv';
        $content = $this->getLayout()->createBlock('awadvancedreportsmod/advanced_region_grid')->setIsExport(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportOrderedExcelAction()
    {
        $fileName = 'region.xml';
        $content = $this->getLayout()->createBlock('awadvancedreportsmod/advanced_region_grid')->setIsExport(true)
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}
