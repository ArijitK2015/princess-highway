<?php
/**
cron test button

http://bincani.jacklondon.com.au/index.php/factoryx/shippedfrom_cron_test/testStoreSalesReport/key/3bcdaf312e85f2dd08bf901384e1b2f5/

Mage_Adminhtml_Block_System_Config_Form_Field
FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button
*/

class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button_TestReport extends FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button {

    /**
     * @param array $params
     * @return string
     */
    public function getRoute($params = array()) {
        //$url = Mage::helper('adminhtml')->getUrl("shippedfrom/adminhtml_cron_test/testStoreSalesReport/index", $params);
        $url = Mage::helper('adminhtml')->getUrl("adminhtml/shippedfrom_cron_test/testStoreSalesReport/index", $params);
        //Mage::helper('shippedfrom')->log(sprintf("%s->url=%s", __METHOD__, $url) );
        return $url;
    }

}