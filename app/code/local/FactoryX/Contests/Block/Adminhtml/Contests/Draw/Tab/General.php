<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Draw_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the general tab to draw a winner
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('draw_form', array('legend' => Mage::helper('contests')->__('Winner(s) details')));

		// Add a field to get the number of winners to be drawn
        $fieldset->addField('amount', 'text', array(
            'label' => Mage::helper('contests')->__('How many winner(s) do you want to draw ?'),
            'class' => 'required-entry validate-not-negative-number',
            'required' => true,
            'name' => 'amount',
        ));

        return parent::_prepareForm();
    }
	
}