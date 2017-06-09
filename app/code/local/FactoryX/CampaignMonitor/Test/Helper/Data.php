<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_CampaignMonitor_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Instance of tested object
     * @return FactoryX_CampaignMonitor_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('campaignmonitor');
    }
}