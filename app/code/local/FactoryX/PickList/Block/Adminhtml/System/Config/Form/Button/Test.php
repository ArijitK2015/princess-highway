<?php
/**
Self test button

Mage_Adminhtml_Block_System_Config_Form_Field
*/

class FactoryX_PickList_Block_Adminhtml_System_Config_Form_Button_Test extends FactoryX_PickList_Block_Adminhtml_System_Config_Form_Button {

    /**
     * @param array $params
     * @return string
     */
    public function getRoute($params = array()) {
        $url = Mage::helper('adminhtml')->getUrl("adminhtml/test/index", $params);
        //Mage::log(sprintf("%s->url=%s", __METHOD__, $url) );
        return $url;
    }
}