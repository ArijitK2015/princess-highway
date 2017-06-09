<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Details_Form
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Details_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the details page
	 */
	protected function _prepareForm() 
	{
		$form = new Varien_Data_Form(array(
                                        'id' => 'details_form',
                                        'action' => $this->getUrl('*/*/chooselayout', array('id' => $this->getRequest()->getParam('id'))),
                                        'method' => 'post',
										'enctype' => 'multipart/form-data'
                                     )
        );
 
        $form->setUseContainer(true);
        $this->setForm($form);
		
		// We retrieve the data from the session and store it in a data array to populate the form
		if (Mage::getSingleton('adminhtml/session')->getAmount()) 
		{
			$data['amount'] = Mage::getSingleton('adminhtml/session')->getAmount();
		} 
		if (Mage::getSingleton('adminhtml/session')->getSlider()) 
		{
			$data['slider'] = Mage::getSingleton('adminhtml/session')->getSlider();
		}
		
		$fieldset = $form->addFieldset('homepage_form', array('legend' => Mage::helper('homepage')->__('Home Page details')));

		// Add a field to get the number of pictures to be displayed
        $fieldset->addField('amount', 'text', array(
            'label' => Mage::helper('homepage')->__('How many picture(s) do you want to display (Up to 5) ?'),
            'class' => 'required-entry validate-not-negative-number',
            'required' => true,
            'name' => 'amount',
        ));
		
		// Add a field to get the number of pictures to be displayed
        $fieldset->addField('slider', 'select', array(
            'label' => Mage::helper('homepage')->__('Do you want a picture slider ?'),
            'name' => 'slider',
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
			'class' => 'required-entry',
            'required' => true,
			'notes'	=> 'The number of pictures provided above includes the picture displayed in the slider'
		));
		
		// We fill the form based on the retrieved data
        if (isset($data)) $form->setValues($data);
		
        return parent::_prepareForm();
    }
	
}