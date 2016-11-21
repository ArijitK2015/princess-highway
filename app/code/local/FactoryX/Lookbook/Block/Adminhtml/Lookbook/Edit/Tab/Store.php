<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Store
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the store tab for the edit lookbook page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
		
        $fieldset = $form->addFieldset('lookbook_form', array('legend' => Mage::helper('lookbook')->__('Store Configuration')));
		
		// Add the store selector form
		$fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('lookbook')->__('Store View'),
                'title' => Mage::helper('lookbook')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
		
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getLookbookData()) 
		{
            $form->setValues(Mage::getSingleton('adminhtml/session')->getLookbookData());
            Mage::getSingleton('adminhtml/session')->setLookbookData(null);
        } 
		elseif (Mage::registry('lookbook_data')) 
		{
            $form->setValues(Mage::registry('lookbook_data')->getData());
        }
	}
	
}