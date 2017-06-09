<?php

/**
 * Class FactoryX_CreditmemoReasons_Model_System_Config_Source_Reasons
 */
class FactoryX_CreditmemoReasons_Model_System_Config_Source_Reasons  {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        $reasons = Mage::getResourceModel('creditmemoreasons/reason_collection')->setOrder('sort_order','ASC');
        $options = array();
        foreach ($reasons as $reason) {
            $options[] = array(
                'value' => $reason->getIdentifier(),
                'label' => $reason->getTitle()
            );
        }
        return $options;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     * @return array
     */
    public function toOptionHash($valueField='id', $labelField='name') {
        $res = array();
        $reasons = Mage::getResourceModel('creditmemoreasons/reason_collection')->setOrder('sort_order','ASC');
        foreach ($reasons as $reason) {
            $res[$reason->getData($valueField)] = $reason->getData($labelField);
        }
        return $res;
    }
}