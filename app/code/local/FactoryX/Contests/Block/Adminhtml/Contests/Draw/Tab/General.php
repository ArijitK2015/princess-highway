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
            'class' => 'validate-not-negative-number',
            'name' => 'amount',
        ));
		
		// Add a field to be able to choose winners from a specific state
        $fieldset->addField('state_enable', 'select', array(
            'label' => Mage::helper('contests')->__('Choose winner(s) from specific state ?'),
            'class' => 'required-entry',
            'required' => true,
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
            'name' => 'state_enable',
        ));
		
		$states = Mage::helper('contests')->getStates();
		$valuesStates = array();
		$valuesStates[] = array('value' => 0, 'label' => Mage::helper('contests')->__('Please Select'));
		foreach ($states as $key => $state)
		{
			$valuesStates[] = array('value' => $key, 'label' => $state);
		}
		
		$fieldset->addField('state', 'select', array(
            'label' => Mage::helper('contests')->__('States Selector'),
			'values' => $valuesStates,
            'name' => 'state',
        ));
		
		
		$fieldset->addField('one_per_state', 'checkbox', array(
            'label' => Mage::helper('contests')->__('Draw X winner(s) per state selected'),
			'checked' => false,
			'value'  => '1',
            'name'	 => 'one_per_state',
			'note'  => Mage::helper('contests')->__('Where X is the amount provided above.')
        ));
		
		// Remove the first value in the multiselect
		array_shift($valuesStates);
		
        $fieldset->addField('states', 'multiselect', array(
            'label' => Mage::helper('contests')->__('States Selector'),
			'values' => $valuesStates,
            'name' => 'states',
        ));

        return parent::_prepareForm();
    }
	
}