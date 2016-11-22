<?php

/**
 * Class FactoryX_Instagram_Model_Adminhtml_Observer
 */
class FactoryX_Instagram_Model_Adminhtml_Observer{
    /**
     * @param $observer
     */
    public function renderCustomTemplate($observer) {
        $form = $observer->getEvent()->getForm();
        $customValues = $form->getElement('instagram_hashtag');
        if ($customValues) {
            $customValues->setRenderer(
                Mage::app()->getLayout()->createBlock('instagram/adminhtml_product')
            ); //set a custom renderer to your attribute
        }
    }
}