<?php

class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the general tab to edit a homepage
	 */
	protected function _prepareForm() 
	{
		// Model registered as a homepage
		$model = Mage::registry('homepage');

        $form = new Varien_Data_Form();
        $this->setForm($form);
		
        $fieldset = $form->addFieldset('homepage_form', array('legend' => Mage::helper('homepage')->__('General Information')));
		
		// We retrieve the data from the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getHomepageData()) 
		{
			$data = Mage::getSingleton('adminhtml/session')->getHomepageData();
			Mage::getSingleton('adminhtml/session')->setHomepageData(null);
		} 
		elseif (Mage::registry('homepage_data')) 
		{
			$data = Mage::registry('homepage_data')->getData();
		}
		
		// Dummy values for disabled field
		$data['amount_text'] = $data['amount'];
		$data['slider_text'] = $data['slider'];
		
		// Dummy values for empty dates
		if (array_key_exists('start_date',$data) && $data['start_date'] == "0000-00-00 00:00:00") $data['start_date'] = "";
		if (array_key_exists('end_date',$data) && $data['end_date'] == "0000-00-00 00:00:00") $data['end_date'] = "";

		// Field for the title of the homepage
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('homepage')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
		
		// Field for the status (Enabled or not)
		$fieldset->addField('status', 'select', array(
            'label' => Mage::helper('homepage')->__('Status'),
            'name' => 'status',
            'values' => array(
				array(
                    'value' => 2,
                    'label' => Mage::helper('homepage')->__('Automatic'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('homepage')->__('Disabled'),
                ),
				array(
                    'value' => 1,
                    'label' => Mage::helper('homepage')->__('Enabled'),
                )
            ),
			'note'      => Mage::helper('homepage')->__('The automatic status will use start and end dates to automatically enable and disable the homepage.')
		));
		
		$fieldset->addField('amount_text', 'text', array(
            'label' => Mage::helper('homepage')->__('Number of pictures'),
            'name' => 'amount_text',
			'disabled' => true
        ));
		
		$fieldset->addField('amount', 'hidden', array(
			  'name'	=> 'amount'
		));
		
		$fieldset->addField('slider_text', 'select', array(
            'label' => Mage::helper('homepage')->__('Slider'),
            'name' => 'slider_text',
			'disabled' => true,
			'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('homepage')->__('No'),
                ),
				array(
                    'value' => 1,
                    'label' => Mage::helper('homepage')->__('Yes'),
                )
			),
        ));
		
		$fieldset->addField('slider', 'hidden', array(
			  'name'	=> 'slider'
		));
		
		$fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('homepage')->__('Sort Order'),
            'name' => 'sort_order'
        ));
		
		// Output format for the start and end dates
		$outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

		// Field for the start date validated with javascript
        $fieldset->addField('start_date', 'date', array(
            'name' => 'start_date',
            'label' => $this->__('Start Date'),
            'title' => $this->__('Start Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
			'class' => 'validate-date-au',
			'note'      => Mage::helper('homepage')->__('Only with automatic status: homepage will automatically start at 1am on the start date.')
        ));
		
		// Field for the end date validated with javascript
		$fieldset->addField('end_date', 'date', array(
            'name' => 'end_date',
            'label' => $this->__('End Date'),
            'title' => $this->__('End Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
			'class' => 'validate-date-au',
			'note'      => Mage::helper('homepage')->__('Only with automatic status: homepage will automatically end at 1am on the end date.')
        ));

		// We fill the form based on the retrieved data
        $form->setValues($data);
		
        return parent::_prepareForm();
    }
	
}