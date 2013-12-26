<?php
class Mage_Adminhtml_Block_Sales_Order_Grid_Renderer_CustomerGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    // Holds an associative array with customer_group_id and the associated label
    private static $_customerGroups = array(); // "singleton"

    public static function getCustomerGroupsArray() {
        // Make sure the static property is only populated once
        if (count(self::$_customerGroups) == 0) {
            //$customer_group = new Mage_Customer_Model_Group();
            //$customer_groups = $customer_group->getCollection()->toOptionHash();
            // over ride entire hash
            $customer_groups = array();
			$customer_groups[0] = 'Guest'; 
            $customer_groups[1] = 'Customer';
            self::$_customerGroups = $customer_groups;
            //Mage::log(sprintf("%s->CustomerGroup:%s", __METHOD__, print_r(self::$_customerGroups, true)));
        }    
        return self::$_customerGroups;
    }

    // Transforms the customer_group_id into corresponding label
    public function render(Varien_Object $row)
    {
        $val = $this->_getValue($row);
        $customer_groups = self::getCustomerGroupsArray();
        $retVal = isset($customer_groups[$val]) ? $customer_groups[$val] : 'Guest';
        return $retVal;
    }

}