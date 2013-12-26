<?php
/**
 * Data helper for dashboard
 *
 * @category   Mage
 * @package    FactoryX_TwelveHoursRange
 * @author     Raph <raphael@factoryx.com.au>
 */

class FactoryX_TwelveHoursRange_Helper_Adminhtml_Dashboard_Data extends Mage_Adminhtml_Helper_Dashboard_Data
{
    /**
     * Prepare array with periods for dashboard graphs
     *
     * @return array
     */
    public function getDatePeriods()
    {
		// Add 12h to the array of periods
        return array(
            '12h'=>$this->__('Last 12 Hours'),
            '24h'=>$this->__('Last 24 Hours'),
            '7d'=>$this->__('Last 7 Days'),
            '1m'=>$this->__('Current Month'),
            '1y'=>$this->__('YTD'),
            '2y'=>$this->__('2YTD')
        );
    }
}
