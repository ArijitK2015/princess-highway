<?php

class FactoryX_PickList_Helper_Data extends Mage_Core_Helper_Data {
	
	protected $_weekdays;
    protected $_locale;
    protected $orderStatuses;
	protected $logFileName = 'factoryx_picklist.log';

	/**
	 * get available order source
	 * return array
	 */
    public function getOrderSourceOptions() {
        return array(
            array( 'value'=>'all', 'label' => $this->__('All'), 'default' => 1 ),
            array( 'value'=>'magento', 'label' => $this->__('Magento') ),
            array( 'value'=>'ebay', 'label' => $this->__('eBay') )
        );
    } 

	/**
	select label from sales_order_status where status = 'processing_stage2';
	*/
   	public function getStatusLabel($status) {
   	    //$this->log(sprintf("%s->statusLabel: %s", __METHOD__, $status));
   	    
    	if (preg_match("/1.4.2.0/", Mage::getVersion())) {
    		//$statusLabel = $this->status;
    		$statusLabel = $status;
    	}
    	else {   		
			/*
			$orderStatus = Mage::getModel('sales/order_status')->getCollection()->addFieldToFilter('status', array('eq'=>$status));
			$statusLabel = "Unknown";
			if (count($orderStatus) >= 1) {
				$statusLabel = $orderStatus->getFirstItem()->getLabel();
			}
            */
			if (!$this->orderStatuses) {
			    $this->orderStatuses = Mage::getModel('sales/order_status')->getCollection();
			}
			$statusLabel = "Unknown";
			foreach($this->orderStatuses as $orderStatus) {
			    //$this->log(sprintf("%s->statusLabel: %s == %s", __METHOD__, $status, $orderStatus->getStatus()));
			    if ($orderStatus->getStatus() == $status) {
			        $statusLabel = $orderStatus->getLabel();
			    }
			}
		}
		//$this->log(sprintf("%s->statusLabel=%s", __METHOD__, $statusLabel));
   		return $statusLabel;
    } 


	/**
	 *
	 * return array
	 */
    public function getOptions() {
        return array(
            array( 'value'=>'today', 'label' => $this->__('Today'), 'default' => 1 ),
            array( 'value'=>'yesterday', 'label' => $this->__('Yesterday') ),
            array( 'value'=>'last_7_days', 'label' => $this->__('Last 7 days') ),
            array( 'value'=>'last_week', 'label' => $this->_getLastWeekLabel()  ),
            array( 'value'=>'last_business_week', 'label' => $this->__('Last business week (Mon - Fri)') ),
            array( 'value'=>'this_month', 'label' => $this->__('This month') ),
            array( 'value'=>'last_month', 'label' => $this->__('Last month') ),
            array( 'value'=>'custom', 'label' => $this->__('Custom date range') ),
        );
    } 

	/**
	 *
	 */
    public function getRangeValues() {
        $ctz = date_default_timezone_get();
        //$this->log("getRangeValues()=" . $ctz);
        $mtz =  Mage::app()->getStore()->getConfig('general/locale/timezone');
        //$this->log("getRangeValues()=" . $mtz);
        @date_default_timezone_set( $mtz );
	
		$firstDay = $this->_getWeekDayName( $this->_getFirstWeekDay() );
		$lastDay = $this->_getWeekDayName( $this->_getLastWeekDay() );
	
        $format = $this->getDateFormat();
        $res = array(
            array(
               'key'  => 'today',
               'from' => strftime( $format ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'yesterday',
               'from' => strftime( $format, strtotime('yesterday') ),
               'to'   => strftime( $format, strtotime('yesterday') )
            ),
            array(
               'key'  => 'last_7_days',
               'from' => strftime( $format, strtotime('- 7 days') ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'last_week',
               'from' => strftime( $format, strtotime( $firstDay ) ) === strftime( $format, strtotime( 'today' ) ) ? strftime( $format, strtotime( 'last '.$firstDay ) ) : strftime( $format, strtotime('last week '.$firstDay.' - 7 days') ),
               'to'   => strftime( $format, strtotime('last week '.$lastDay ) )
            ),
            array(
               'key'  => 'last_business_week',
               'from' => strftime( $format, strtotime( 'monday' ) ) === strftime( $format, strtotime( 'today' ) ) ? strftime( $format, strtotime( 'last monday' ) ) : strftime( $format, strtotime('last week mon - 7 days') ),
               'to'   => strftime( $format, strtotime('last week fri') )
            ),
            array(
               'key'  => 'this_month',
               'from' => strftime( $format, strtotime( date('m/01/y') ) ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'last_month',
               'from' => strftime( $format, strtotime( date('m/01/y', strtotime( 'last month' ) ) ) ),
               'to'   => strftime( $format, strtotime( date('m/01/y').' - 1 day' ) )
            ),
        );
        @date_default_timezone_set( $ctz );
        return $res;
    }
    
    protected function _getLastWeekLabel() {
		$firstDayNum = Mage::getStoreConfig('general/locale/firstday') ? Mage::getStoreConfig('general/locale/firstday') : 0;
		$lastDayNum = $firstDayNum + 6;
		$lastDayNum = $lastDayNum > 6 ? $lastDayNum - 7 : $lastDayNum;
		return $this->__('Last week').' ('.substr($this->getWeekday($firstDayNum), 0,3).' - '.substr($this->getWeekday($lastDayNum),0,3 ).')';
    }
    
    protected function _getFirstWeekDay()
    {
		return Mage::getStoreConfig('general/locale/firstday')?Mage::getStoreConfig('general/locale/firstday'):0;
    }

    protected function _getLastWeekDay()
    {
		$firstDayNum = Mage::getStoreConfig('general/locale/firstday')?Mage::getStoreConfig('general/locale/firstday'):0;
		$lastDayNum = $firstDayNum + 6;
		return $lastDayNum > 6 ? $lastDayNum - 7 : $lastDayNum;
    }

    protected function _getWeekDayName($index)
    {
		$days = array(
		    0 => 'sun',
		    1 => 'mon',
		    2 => 'tue',
		    3 => 'wed',
		    4 => 'thu',
		    5 => 'fri',
		    6 => 'sat',
		);	
		return isset($days[$index])? $days[$index] : null;
    }
    
    public function getWeekday($weekday) {
    	//$this->log("getWeekday: " . $weekday);
        if (!$this->_weekdays) {
            $this->_weekdays = Mage::app()->getLocale()->getOptionWeekdays();
        }
        foreach ($this->_weekdays as $day) {
            if ($day['value'] == $weekday) {
                return $day['label'];
            }
        }
    }
    
	public function getDateFormat() {
		//$date_format = $this->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		// Note. %e places a space in the day field which confuses the calendar widget
		$date_format = "%d/%m/%y";
		//$this->log("date_format=" . $date_format);
        return $date_format;
    }
    
    public function getLocale() {
		if ( !$this->_locale ) {
            $this->_locale = Mage::app()->getLocale();
        }
        return $this->_locale;
    }
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
    
}