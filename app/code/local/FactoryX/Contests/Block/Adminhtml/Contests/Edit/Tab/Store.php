<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the store tab for the edit contest page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contests_form', array('legend' => Mage::helper('contests')->__('Store Configuration')));
		
		// Add the store selector form
		$fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('contests')->__('Store View'),
                'title' => Mage::helper('contests')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
		
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getContestsData()) 
		{
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContestsData());
            Mage::getSingleton('adminhtml/session')->setContestsData(null);
        } 
		elseif (Mage::registry('contests_data')) 
		{
            $form->setValues(Mage::registry('contests_data')->getData());
        }
	}
	
}