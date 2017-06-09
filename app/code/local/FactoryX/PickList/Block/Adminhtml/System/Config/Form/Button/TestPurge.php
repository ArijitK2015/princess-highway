<?php
/**
cron test button

Mage_Adminhtml_Block_System_Config_Form_Field
FactoryX_PickList_Block_Adminhtml_System_Config_Form_Button
*/

class FactoryX_PickList_Block_Adminhtml_System_Config_Form_Button_TestPurge extends FactoryX_PickList_Block_Adminhtml_System_Config_Form_Button {


    /**
     * @param array $params
     * @return string
     */
    public function getRoute($params = array()) {
        $url = Mage::helper('adminhtml')->getUrl("adminhtml/cron_test/testDailyOutputDirPurge/index", $params);
        //Mage::log(sprintf("%s->url=%s", __METHOD__, $url) );
        return $url;
    }

}