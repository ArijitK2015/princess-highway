<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_List
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_List extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the list tab for the edit contest page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contests_form', array('legend' => Mage::helper('contests')->__('List Configuration')));
		
		// Field to choose if the contest is the listing page or not
		$fieldset->addField('is_in_list', 'select', array(
            'label' => Mage::helper('contests')->__('Display in Contests listing page ?'),
            'name' => 'is_in_list',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('No'),
                )
            )
		));
		
		try 
		{
			// Get the WYSIWYG config if it's enabled
            $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        } 
		catch (Exception $ex) 
		{
            $config = null;
        }
		
		// Field for the list text
		$fieldset->addField('list_text', 'editor', array(
            'label' => Mage::helper('contests')->__('List Text'),
            'name' => 'list_text',
			'style' => 'width:700px; height:500px;',
            'config' => $config,
			'note' => Mage::helper('contests')->__('Text which will be displayed in the Contests listing page. E.g.: "To celebrate Alannah Hill new collection, we have a special price to give away..."')
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