<?php

/**
 * Class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Edit_Tab_General
 */
class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the general tab to edit a lookbook
	 */
	protected function _prepareForm()
	{
		// Model registered as a lookbook
		Mage::registry('customersurvey');

		$form = new Varien_Data_Form();
		$this->setForm($form);

		// General Information
		$fieldset = $form->addFieldset('customersurvey_form', array(
				'legend'    => Mage::helper('customersurvey')->__('General Information'),
				//'class'     => 'fieldset-wide',
				'expanded'  => true // open
			)
		);

		// We retrieve the data from the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getCustomersurveyData())
		{
			$data = Mage::getSingleton('adminhtml/session')->getCustomersurveyData();
			Mage::getSingleton('adminhtml/session')->setCustomersurveyData(null);
		}
		elseif (Mage::registry('customersurvey_data'))
		{
			$data = Mage::registry('customersurvey_data')->getData();
		}
		else $data = array();

		// Field for the title of the lookbook
		$fieldset->addField('title', 'text', array(
			'label' => Mage::helper('customersurvey')->__('Title'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'title',
		));

		// Generate the product chooser widget
		$config = array(
			'input_id'    => 'code',
			'input_name'  => 'code',
			'input_label' => Mage::helper('customersurvey')->__('Coupon Code upon Completion'),
			'button_text' => 'Select Coupon...'
		);

		$model = Mage::getModel('salesrule/rule');
		if ($data['code'])
		{
			$model->setData(array('code' => $data['code']));
			unset($data['code']);
		}

		$chooserBlock = 'customersurvey/adminhtml_salesrule_rule_widget_chooser';
		// Create our chooser with our custom block
		Mage::helper('chooserwidget')->createChooser($model, $fieldset, $config, $chooserBlock);

		// Field for the status (Enabled or not)
		$fieldset->addField('enabled', 'select', array(
			'label' => Mage::helper('lookbook')->__('Status'),
			'name' => 'enabled',
			'values' => array(
				array(
					'value' => 0,
					'label' => Mage::helper('lookbook')->__('Inactive'),
				),
				array(
					'value' => 1,
					'label' => Mage::helper('lookbook')->__('Active'),
				)
			)
		));

		// We fill the form based on the retrieved data
		$form->setValues($data);

		return parent::_prepareForm();
	}
}