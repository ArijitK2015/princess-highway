<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Terms
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Terms extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form for the terms and conditions tab of the edit contest page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contests_form', array('legend' => Mage::helper('contests')->__('Terms &amp; Conditions')));
		
		try 
		{
			// Get the WYSIWYG config if it's enabled
            $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        } 
		catch (Exception $ex) 
		{
            $config = null;
        }

		// Field for the terms and conditions (handling WYSIWYG)
        $fieldset->addField('terms', 'editor', array(
            'name' => 'terms',
            'label' => Mage::helper('contests')->__('Terms & Conditions'),
            'title' => Mage::helper('contests')->__('Terms & Conditions'),
            'style' => 'width:700px; height:500px;',
            'config' => $config
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