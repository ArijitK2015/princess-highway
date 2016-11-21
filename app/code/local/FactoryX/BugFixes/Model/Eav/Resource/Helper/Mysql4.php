<?php

/**
 * Integer being cast to decimal bug: http://magento.stackexchange.com/questions/105244/magento-1-admin-product-grid-column-value-disappear-on-name-search/105421
 * Class FactoryX_BugFixes_Model_Eav_Resource_Helper_Mysql4
 */
class FactoryX_BugFixes_Model_Eav_Resource_Helper_Mysql4 extends Mage_Eav_Model_Resource_Helper_Mysql4
{
    /**
     * Groups selects to separate unions depend on type
     *
     * @param array $selects
     * @return array
     */
    public function getLoadAttributesSelectGroups($selects)
    {
        $mainGroup  = array();
        foreach ($selects as $eavType => $selectGroup) {
            $mainGroup = array_merge($mainGroup, $selectGroup);
        }
        return $mainGroup;
    }
}