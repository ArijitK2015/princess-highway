<?php
/*
Mage::helper('picklist/date')

Mage::helper('core')->formatDate(new Zend_Date(time()), $format, bool $showTime);

$format = (full, long, medium, short)
full    = Wednesday, 3 September 2014
full    = Wednesday, 3 September 2014 3:18:34 AM UTC
long    = 3 September 2014
long    = 3 September 2014 3:18:34 AM UTC
medium  = 03/09/2014
medium  = 03/09/2014 3:18:34 AM
short   = 3/09/14
short   = 3/09/14 3:18 AM
*/

/**
 * Class FactoryX_PickList_Helper_Date
 */
class FactoryX_PickList_Helper_Date extends Mage_Core_Helper_Data {

    const SECS_IN_DAY = 86400;

    /**
     * getDateRange
     *
     * returns a date range
     *
     * @param string $theDay the day
     * @throws Exception
     * @return array array('start' => startTs, 'end' => endTs)
     */
    function getDateRange($theDay = 'mon') {
        
        // get default LOCAL timestamps yesterday
        $tsS = Mage::getModel('core/date')->timestamp() - self::SECS_IN_DAY;
        $tsE = Mage::getModel('core/date')->timestamp() - self::SECS_IN_DAY;

        // mon: [thu, fri, sat]
        if (preg_match("/(mon)/i", $theDay)) {
            $tsS = Mage::getModel('core/date')->timestamp() - (self::SECS_IN_DAY * 4);
            $tsE = Mage::getModel('core/date')->timestamp() - (self::SECS_IN_DAY * 2);
        }
        // tue: [fri, sat, sun, mon]
        else if (preg_match("/(tue)/i", $theDay)) {
            $tsS = Mage::getModel('core/date')->timestamp() - (self::SECS_IN_DAY * 4);
            $tsE = Mage::getModel('core/date')->timestamp() - (self::SECS_IN_DAY * 1);            
        }
        // wed: [sun, mon, tue]
        else if (preg_match("/(wed)/i", $theDay)) {
            $tsS = Mage::getModel('core/date')->timestamp() - (self::SECS_IN_DAY * 3);
            $tsE = Mage::getModel('core/date')->timestamp() - (self::SECS_IN_DAY * 1);
        }
        // t&f: [thu = tues, wed | fri = wed, thur]
        else if (preg_match("/(thu|fri)/i", $theDay)) {
            $tsS = Mage::getModel('core/date')->timestamp() - (self::SECS_IN_DAY * 2);
            $tsE = Mage::getModel('core/date')->timestamp() - (self::SECS_IN_DAY * 1);            
        }
        else {
            throw new Exception(sprintf("the following day '%s' has no order range configured!", $theDay));
        }
        $retVal = array(
            'start' => $tsS,
            'end'   => $tsE,
        );
        return $retVal;
    }

    /**
     * convertDateToTs
     *
     * convert date string dd/mm/yyyy to UTC timestamp
     *
     * @param string $date e.g. 02/09/14 or 02/09/2014
     * @return long timestamp
     */
    public function convertDateToTs($date) {
        $parts = explode("/", $date);
        // mktime(hour, min, secs, month, day, year, int)
        $ts = mktime(0, 0, 0, $parts[1], $parts[0], $parts[2]);
        return $ts;
    }

    /**
     * getStartOfDay
     *
     * gets the start of the day
     *
     * @param long $ts timestamp
     * @param bool $gmt convert to gmt
     * @return long $ts
     */
    function getStartOfDay($ts, $gmt = false) {
        $retVal = mktime(0, 0, 0, date('m', $ts), date('d', $ts), date('Y', $ts));
        if ($gmt) {
            $retVal = strtotime(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $retVal));
        }
        return $retVal;
    }

    /**
     * getEndOfDay
     *
     * gets the end of the day
     *
     * @param long $ts timestamp
     * @param bool $gmt convert to gmt
     * @param bool $previousDay
     * @return long $ts
     */
    function getEndOfDay($ts, $gmt = false, $previousDay = false) {
        $diff = (3600 * 24) - 1;
        if ($previousDay) {
            $diff = -1;
        }
        $retVal = mktime(0, 0, 0, date('m', $ts), date('d', $ts), date('Y', $ts)) + $diff;
        if ($gmt) {
            $retVal = strtotime(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $retVal));
        }        
        return $retVal;
    }
    
}