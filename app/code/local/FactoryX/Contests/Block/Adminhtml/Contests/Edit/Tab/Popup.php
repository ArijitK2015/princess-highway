<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Popup extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the popup tab for the edit contest page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contests_form', array('legend' => Mage::helper('contests')->__('Popup Configuration')));
		
		// Field to choose if the contest is a popup or not
		$fieldset->addField('is_popup', 'select', array(
            'label' => Mage::helper('contests')->__('Display a popup for the contest when accessing the website'),
            'name' => 'is_popup',
            'values' => array(
				array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Yes'),
                )
            ),
			'value'	=>	0,
			'note' => Mage::helper('contests')->__('There can be only ONE contest PER STORE using the popup at a time.')
		));
		
		// Field for the popup text
		$fieldset->addField('popup_text', 'text', array(
            'label' => Mage::helper('contests')->__('Popup Text'),
            'name' => 'popup_text',
			'note' => Mage::helper('contests')->__('Text that will be displayed in the popup. E.g.: "Win a $500 wardrobe"')
        ));
		
		// Field for the popup text
		$fieldset->addField('popup_referers', 'editor', array(
            'label' => Mage::helper('contests')->__('Popup Allowed Referers'),
			'title' => Mage::helper('contests')->__('Popup Allowed Referers'),
            'name' => 'popup_referers',
			'note' => Mage::helper('contests')->__('You can allow a list of domains (delimited by comma). This will be checked each time someone accesses the website and then only popup if the referrer is one of the allowed domain.Left blank not to limit.')
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