<?php
/**
add "NOT LOGGED IN" to the customer group list as a filter is applied to 
app/code/core/Mage/Adminhtml/Model/System/Config/Source/Customer/Group/Multiselect.php
<source_model>adminhtml/system_config_source_customer_group_multiselect</source_model>
*/
class FactoryX_CampaignMonitor_Model_System_Config_Source_GroupCollection  {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        $groups = Mage::getModel('customer/group')->getCollection();        
        $options = array();
        foreach ($groups as $group) {
            $options[] = array(
                'value' => $group->getCustomerGroupId(),
                'label' => $group->getCustomerGroupCode()
            );
        }
        return $options;
    }
}