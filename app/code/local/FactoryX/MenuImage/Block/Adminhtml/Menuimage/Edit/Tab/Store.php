<?php

/**
 * Class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Edit_Tab_Store
 */
class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the store tab for the edit menuimage page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
		
        $fieldset = $form->addFieldset('menuimage_form', array('legend' => Mage::helper('menuimage')->__('Store Configuration')));
		
		// Add the store selector form
		$fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('menuimage')->__('Store View'),
                'title' => Mage::helper('menuimage')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
		
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getMenuimageData())
		{
            $form->setValues(Mage::getSingleton('adminhtml/session')->getMenuimageData());
            Mage::getSingleton('adminhtml/session')->setMenuimageData(null);
        } 
		elseif (Mage::registry('menuimage_data'))
		{
            $form->setValues(Mage::registry('menuimage_data')->getData());
        }
	}
	
}