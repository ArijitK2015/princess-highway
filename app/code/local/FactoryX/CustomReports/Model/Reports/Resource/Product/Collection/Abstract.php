<?php
/**
 * Only with old versions of FactoryX_CustomGrids
 *
 * what was th reason for this?
 */
if (
    (string)Mage::getConfig()->getModuleConfig('FactoryX_CustomGrids')->active == 'true'
    &&
    version_compare(Mage::getConfig()->getModuleConfig('FactoryX_CustomGrids')->version,"0.1.0", "<=")
) {
    /**
     * Class FactoryX_CustomReports_Model_Reports_Resource_Product_Collection_Abstract
     */
    class FactoryX_CustomReports_Model_Reports_Resource_Product_Collection_Abstract extends FactoryX_CustomGrids_Model_Reports_Resource_Product_Collection {}
}
else {
    /**
     * Class FactoryX_CustomReports_Model_Reports_Resource_Product_Collection_Abstract
     */
    class FactoryX_CustomReports_Model_Reports_Resource_Product_Collection_Abstract extends Mage_Reports_Model_Resource_Product_Collection {}
}
