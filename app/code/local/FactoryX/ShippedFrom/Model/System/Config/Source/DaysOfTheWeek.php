<?php
/**
 */

class FactoryX_ShippedFrom_Model_System_Config_Source_DaysOfTheWeek extends Varien_Object {

    /**
     * @return array
     */
    public function toOptionArray() {

        $options = array();
                
        $timestamp = strtotime('next Sunday');
        $days = array();
        for ($i = 0; $i < 7; $i++) {
            //$options[$i] = strftime('%A', $timestamp);
            //$options[] = array('value' => $i, 'label' => strftime('%A', $timestamp));
            $options[] = array('value' => date('N', $timestamp), 'label' => date('l', $timestamp));
            $timestamp = strtotime('+1 day', $timestamp);
        }
        
        return $options;
    }
}
