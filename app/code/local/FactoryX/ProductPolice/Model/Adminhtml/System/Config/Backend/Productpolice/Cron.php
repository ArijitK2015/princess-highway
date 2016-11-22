<?php
/**
 * Who:  Alvin Nguyen
 * When: 29/09/2014
 * Why:  
 */

class FactoryX_ProductPolice_Model_Adminhtml_System_Config_Backend_Productpolice_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/productpolice_recheck/schedule/cron_expr';

    protected function _afterSave()
    {
        $enable    = $this->getData('groups/options/fields/cron_enable/value');
        $frequency = $this->getData('groups/options/fields/frequency/value');

        if ($enable){
            $cronExprArray = array(
                '0',                                # Minute
                '*/'.$frequency,                    # Hour
                '*',                                # Day of the Month
                '*',                                # Month of the Year
                '*',                                # Day of the Week
            );
            $cronExprString = join(' ', $cronExprArray);
        }else{
            $cronExprString = "";
        }

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        }
        catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }
}