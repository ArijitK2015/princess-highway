<?php

/**
 * Class FactoryX_ConditionalAgreement_Model_Observer
 */
class FactoryX_ConditionalAgreement_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function restrictAgreements(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Checkout_Block_Agreements) {
            $agreements = Mage::helper('conditionalagreement')->getRequiredAgreements();
            if ($agreements) {
                $block->setAgreements($agreements);
            }
        }
    }
}