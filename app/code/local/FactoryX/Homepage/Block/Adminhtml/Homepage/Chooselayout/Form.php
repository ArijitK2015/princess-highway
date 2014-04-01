<?php

class FactoryX_Homepage_Block_Adminhtml_Homepage_Chooselayout_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the choose layout page
	 */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                                        'id' => 'chooselayout_form',
                                        'action' => $this->getUrl('*/*/continue', array('id' => $this->getRequest()->getParam('id'))),
                                        'method' => 'post',
										'enctype' => 'multipart/form-data'
                                     )
        );
 
        $form->setUseContainer(true);
        $this->setForm($form);
		
		// If there is a layout set in the session
		if (Mage::getSingleton('adminhtml/session')->getLayout()) 
		{
			// We set a data array to store it and we will use it to populate the form
			$data['layout'] = Mage::getSingleton('adminhtml/session')->getLayout();
		} 
		
		$fieldset = $form->addFieldset('homepage_form', array('legend' => Mage::helper('homepage')->__('Choose your layout')));
		
		// Add our custom pickable image type
		$fieldset->addType('radio_images','FactoryX_Homepage_Model_Varien_Data_Form_Element_RadioImages');
		
		// Get variables from session
		$amountOfPix = Mage::getSingleton('adminhtml/session')->getAmount();
		$slider = Mage::getSingleton('adminhtml/session')->getSlider();
		
		// Get corresponding folder
		$layoutFolder = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default')).DS.'images'.DS.'factoryx'.DS.'homepage';
		$subfolder = $amountOfPix.'-layout';
		
		// Get list of corresponding pictures
		$layoutPix = Mage::helper('homepage')->dirFiles($layoutFolder,$subfolder,$slider);
		
		// Counter
		$count = 1;
		
		// Display possible choices using our custom pickable image type
		$fieldset->addField('layout', 'radio_images', array(
			'label'     => Mage::helper('core')->__('Layouts'),
			'name'      => 'layout',
			'onclick' => "",
			'onchange' => "",
			'values' => $layoutPix,
			'disabled' => false,
			'readonly' => false,
			'tabindex' => 1
		));
		
		// We fill the form based on the retrieved data
		if (isset($data)) $form->setValues($data);
		
        return parent::_prepareForm();
    }
	
}