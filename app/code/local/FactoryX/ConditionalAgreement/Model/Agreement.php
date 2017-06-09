<?php

class FactoryX_ConditionalAgreement_Model_Agreement extends Mage_Core_Model_Config_Data {

    /*
    get the terms & conditions
    
    @return array(
        array('value' => 0, 'label' =>'First item'),
        array('value' => 1, 'label' => 'Second item'),
        array('value' => 2, 'label' =>'third item'),
        // and so on...
    );
    */
    public function toOptionArray() {
        $options = array();

        $websites = Mage::app()->getWebsites();
        $code = $websites[1]->getDefaultStore()->getCode();
        $storeId = Mage::app()->getStore($code)->getId();

        $agreements = Mage::getModel('checkout/agreement')->getCollection()
            ->addStoreFilter($storeId);

        foreach($agreements as $id => $agreement) {
            $options[] = array(
                'value' => $id,
                'label' => $agreement->getName() . " " . ($agreement->getIsActive() ? "(Active)" : "(Inactive)")
            );
        }
        //Mage::helper('conditionalagreement')->log(sprintf("%s->options=%s", __METHOD__, print_r($options, true)) );
        return $options;
    }

}
?>