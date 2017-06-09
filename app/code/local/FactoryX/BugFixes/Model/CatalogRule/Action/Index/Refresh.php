<?php
/**
 * Fux a bug where timestamp would not be set properly on non GMT install: http://stackoverflow.com/questions/25280095/magento-catalog-price-rule-disappears-at-night
 * Class FactoryX_BugFixes_Model_CatalogRule_Action_Index_Refresh
 */
class FactoryX_BugFixes_Model_CatalogRule_Action_Index_Refresh extends Mage_CatalogRule_Model_Action_Index_Refresh
{
    public function execute()
    {
        $this->_app->dispatchEvent('catalogrule_before_apply', array('resource' => $this->_resource));

        /** @var $coreDate Mage_Core_Model_Date */
        $coreDate  = $this->_factory->getModel('core/date');
        //$timestamp = $coreDate->gmtTimestamp('Today');
        $timestamp = Mage::app()->getLocale()->date(null, null, null, true)->get(Zend_Date::TIMESTAMP);

        foreach ($this->_app->getWebsites(false) as $website) {
            /** @var $website Mage_Core_Model_Website */
            if ($website->getDefaultStore()) {
                $this->_reindex($website, $timestamp);
            }
        }

        $this->_prepareGroupWebsite($timestamp);
        $this->_prepareAffectedProduct();
    }
}