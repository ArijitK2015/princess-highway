<?php

/**
 * Class FactoryX_CustomGrids_Block_Adminhtml_Renderer_CustomerGroup
 */
class FactoryX_CustomGrids_Block_Adminhtml_Renderer_CustomerGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    // Holds an associative array with customer_group_id and the associated label
    private static $_customerGroups = array(); // "singleton"

    /**
     * @return array
     */
    public static function getCustomerGroupsArray()
	{
        // Make sure the static property is only populated once
        if (count(self::$_customerGroups) == 0)
		{
            $customer_group_collection = Mage::getModel('customer/group')->getCollection();
            self::$_customerGroups = $customer_group_collection->toOptionHash();
        }
        return self::$_customerGroups;
    }

    /**
     * Transforms the customer_group_id into corresponding label
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $val = $this->_getValue($row);
        $customer_groups = self::getCustomerGroupsArray();
        $retVal = isset($customer_groups[$val]) ? $customer_groups[$val] : 'Guest';
        return $retVal;
    }

}