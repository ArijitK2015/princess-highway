<?php
/**
 * Who:  Alvin Nguyen
 * When: 29/09/2014
 * Why:  
 */

class FactoryX_ImageCdn_Model_Adminhtml_System_Config_Backend_Imagecdn_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/imagecdn_reupload/schedule/cron_expr';

    protected function _afterSave()
    {
        $enable    = $this->getData('groups/cronjob/fields/status/value');
        $frequency = $this->getData('groups/cronjob/fields/frequency/value');

        if ($enable){
            if ($frequency > 60){
                $frequency = floor($frequency/60);
                $cronExprArray = array(
                    '*',                                # Minute
                    '*/'.$frequency,                    # Hour
                    '*',                                # Day of the Month
                    '*',                                # Month of the Year
                    '*',                                # Day of the Week
                );
            }else{
                $cronExprArray = array(
                    '*/'.$frequency,                    # Minute
                    '*',                                # Hour
                    '*',                                # Day of the Month
                    '*',                                # Month of the Year
                    '*',                                # Day of the Week
                );
            }
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