<?php
/**
run cron button

Mage_Adminhtml_Block_System_Config_Form_Field
FactoryX_StoreLocator_Block_Adminhtml_System_Config_Form_Button
*/

class FactoryX_StoreLocator_Block_Adminhtml_System_Config_Form_Button_RunCron extends FactoryX_StoreLocator_Block_Adminhtml_System_Config_Form_Button {

    /**
     * @param array $params
     * @return string
     */
    public function getRoute($params = array()) {
        $url = Mage::helper('adminhtml')->getUrl("adminhtml/cron_run/runStoreResolver/index", $params);
        //Mage::log(sprintf("%s->url=%s", __METHOD__, $url) );
        return $url;
    }

}