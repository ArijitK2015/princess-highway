<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Choosecat_Form
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Choosecat_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the details page
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
				'id' => 'choosecat_form',
				'action' => $this->getUrl('*/*/new', array('id' => $this->getRequest()->getParam('id'))),
				'method' => 'post',
				'enctype' => 'multipart/form-data'
			)
		);

		$form->setUseContainer(true);
		$this->setForm($form);

		// We retrieve the data from the session and store it in a data array to populate the form
		if (Mage::getSingleton('adminhtml/session')->getLookbookType())
		{
			$data['lookbook_type'] = Mage::getSingleton('adminhtml/session')->getLookbookType();
		}
		if (Mage::getSingleton('adminhtml/session')->getCategoryId())
		{
			$data['category_id'] = Mage::getSingleton('adminhtml/session')->getCategoryId();
		}

		$fieldset = $form->addFieldset('lookbook_form', array('legend' => Mage::helper('lookbook')->__('Lookbook Type')));

		// Field for the lookbook type
		$fieldset->addField('lookbook_type', 'select', array(
			'label' => Mage::helper('lookbook')->__('Lookbook Type'),
			'name' => 'lookbook_type',
			'values' => array(
				array(
					'value' => 'category',
					'label' => Mage::helper('lookbook')->__('Category Lookbook'),
				),
				array(
					'value' => 'images',
					'label' => Mage::helper('lookbook')->__('Images Lookbook'),
				),
				array(
					'value' => 'slideshow',
					'label' => Mage::helper('lookbook')->__('Slideshow Lookbook'),
				),
				array(
					'value' => 'flipbook',
					'label' => Mage::helper('lookbook')->__('Flipbook'),
				)
			)
		));

		$config = array(
			'input_id'    => 'category_id',
			'input_name'  => 'category_id',
			'input_label' => Mage::helper('lookbook')->__('Choose a Category'),
			'button_text' => 'Select Category...'
		);

		$model = Mage::getModel('catalog/category');

		if (isset($data)
			&&
			is_array($data)
			&&
			array_key_exists('category_id', $data)
			&&
			$data['category_id']
		) {
			if (strpos("category/",$data['category_id']) === false) {
				$data['category_id'] = "category/" . $data['category_id'];
			}
			$model->setData(array('category_id' => $data['category_id']));
			unset($data['category_id']);
		}

		// Create a category chooser
		Mage::helper('chooserwidget')->createCategoryChooser($model, $fieldset, $config);

		// We fill the form based on the retrieved data
		if (isset($data)) $form->setValues($data);

		return parent::_prepareForm();
	}

}