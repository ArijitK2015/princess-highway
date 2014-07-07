<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Colour extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the general tab to edit a contest
	 */
	protected function _prepareForm() 
	{
		// Model registered as a contest
		$model = Mage::registry('contests');

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contests_form', array('legend' => Mage::helper('contests')->__('Colour Setting')));

		// Field for the colours of the contest
        $fieldset->addField('background_colour', 'text', array(
            'label' => Mage::helper('contests')->__('Background Colour'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'background_colour'
        ));

        $fieldset->addField('text_colour', 'text', array(
            'label' => Mage::helper('contests')->__('Text Colour'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'text_colour'
        ));

        $fieldset->addField('button_background_colour', 'text', array(
            'label' => Mage::helper('contests')->__('Button Background Colour'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'button_background_colour'
        ));

        $fieldset->addField('button_text_colour', 'text', array(
            'label' => Mage::helper('contests')->__('Button Text Colour'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'button_text_colour'
        ));

        $fieldset->addField('custom_css', 'textarea', array(
            'label' => Mage::helper('contests')->__('Custom CSS'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'custom_css'
        ));

		// We fill the form based on the session or the data registered
        if (Mage::getSingleton('adminhtml/session')->getContestsData()) 
		{
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContestsData());
            Mage::getSingleton('adminhtml/session')->setContestsData(null);
        } 
		elseif (Mage::registry('contests_data')) 
		{
			$data = Mage::registry('contests_data')->getData();	
            $form->setValues($data);		
        }
        return parent::_prepareForm();
    }
	
}