<?php
/**
 */

class FactoryX_PickList_Model_System_Config_Source_CustomerGroup extends Varien_Object {

    /**
     * @return array
     */
    public function toOptionArray() {

        $options = array();

        $options[] = array(
            "value" => "ALL",
            "label" => "All Groups"
        );
        if ($groups = Mage::getModel('customer/group')->getCollection()) {
            foreach ($groups as $group) {
                $options[] = array('value' => $group->getId(), 'label' => $group->getCustomerGroupCode());
            }
        }
        
        return $options;
    }
}
