<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_Store
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the store tab for the edit homepage page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
		
        $fieldset = $form->addFieldset('homepage_form', array('legend' => Mage::helper('homepage')->__('Store Configuration')));
		
		// Add the store selector form
		$fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('homepage')->__('Store View'),
                'title' => Mage::helper('homepage')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
		
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getHomepageData()) 
		{
            $form->setValues(Mage::getSingleton('adminhtml/session')->getHomepageData());
            Mage::getSingleton('adminhtml/session')->setHomepageData(null);
        } 
		elseif (Mage::registry('homepage_data')) 
		{
            $form->setValues(Mage::registry('homepage_data')->getData());
        }
	}
	
}