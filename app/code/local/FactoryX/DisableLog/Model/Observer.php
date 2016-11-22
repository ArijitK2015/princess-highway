<?php

/**
 * Class FactoryX_DisableLog_Model_Observer
 */
class FactoryX_DisableLog_Model_Observer
{
    public function disableLog(Mage_Cron_Model_Schedule $schedule = null)
    {
        if (!Mage::helper('disablelog')->isEnabled())
            return;

        if (Mage::getStoreConfigFlag('dev/log/active'))
            Mage::getModel('core/config')->saveConfig('dev/log/active',false);

        return;
    }
}