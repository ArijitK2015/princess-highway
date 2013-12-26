<?php
/**
 * Adminhtml dashboard order amounts diagram
 *
 * @category   Mage
 * @package    FactoryX_TwelveHoursRange
 * @author     Raph <raphael@factoryx.com.au>
 */

class FactoryX_TwelveHoursRange_Block_Adminhtml_Dashboard_Tab_Amounts extends Mage_Adminhtml_Block_Dashboard_Tab_Amounts
{
    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $this->setDataHelperName('adminhtml/dashboard_order');
        $this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
        $this->getDataHelper()->setParam('website', $this->getRequest()->getParam('website'));
        $this->getDataHelper()->setParam('group', $this->getRequest()->getParam('group'));
		// 12h as the default parameter
        $this->getDataHelper()->setParam(
            'period',
            $this->getRequest()->getParam('period')?$this->getRequest()->getParam('period'):'12h'
            );
        $this->setDataRows('revenue');
        $this->_axisMaps = array(
            'x' => 'range',
            'y' => 'revenue'
        );

        parent::_prepareData();
    }
}

