<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button_TestReport
 */
class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button_TestReport
    extends FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button
{

    /**
     * @param array $params
     * @return string
     */
    public function getRoute($params = array())
    {
        return Mage::helper('adminhtml')->getUrl("adminhtml/shippedfrom_cron_test/testStoreSalesReport/index", $params);
    }

}