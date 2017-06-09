<?php

/**
 * Class FactoryX_ExtraConfigFields_Model_Observer
 */
class FactoryX_ExtraConfigFields_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function renameSystemConfig(Varien_Event_Observer $observer)
    {
        $config = $observer->getConfig();

        // Rename a field
        $config->getNode('sections/general/groups/store_information')->fields->address->label = "Head Office - Contact Address";

        /*
        Rename groups
        $config->getNode('sections/trans_email/groups/ident_sales')->label = "Online Store";
        $config->getNode('sections/trans_email/groups/ident_support')->label = "Retail";
        $config->getNode('sections/trans_email/groups/ident_custom1')->label = "Recruitment";
        $config->getNode('sections/trans_email/groups/ident_custom2')->label = "PR and Media";
        */

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function overrideStoreEmailVariable(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getObject();
        $variables = $object->getVariables();

        if ($storeEmail = Mage::helper('extraconfigfields')->getStoreEmail()) {
            $variables['store_email'] = $storeEmail;
            $object->setVariables($variables);
        }

        return $this;
    }

    /**
     * @param $config
     * @param string $name
     * @param string $label
     */
    protected function _renameField($config, $name, $label)
    {
        $config->getNode($name)->fields->address->label = $label;
    }

    /**
     * @param $config
     * @param string $name
     * @param string $label
     */
    protected function _renameGroup($config, $name, $label)
    {
        $config->getNode($name)->label = $label;
    }
}