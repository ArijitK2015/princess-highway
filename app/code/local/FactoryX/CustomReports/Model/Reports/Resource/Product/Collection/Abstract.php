<?php
if ((string)Mage::getConfig()->getModuleConfig('FactoryX_CustomGrids')->active == 'true'){
    class FactoryX_CustomReports_Model_Reports_Resource_Product_Collection_Abstract extends FactoryX_CustomGrids_Model_Reports_Resource_Product_Collection {}
} else {
    class FactoryX_CustomReports_Model_Reports_Resource_Product_Collection_Abstract extends Mage_Reports_Model_Resource_Product_Collection {}
}
