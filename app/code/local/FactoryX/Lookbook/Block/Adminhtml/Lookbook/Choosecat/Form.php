<?php

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

		// Add our custom pickable image type
		$fieldset->addType('category_select','FactoryX_Lookbook_Model_Varien_Data_Form_Element_CategorySelect');
		
		// Display possible choices using our custom pickable image type
		$fieldset->addField('category_id', 'category_select', array(
			'label'     => Mage::helper('lookbook')->__('Choose a Category'),
			'name'      => 'category_id'
		));
		
		// We fill the form based on the retrieved data
        if (isset($data)) $form->setValues($data);
		
        return parent::_prepareForm();
    }
	
}