<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Developer
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Developer extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the store tab for the edit lookbook page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
		
        $fieldset = $form->addFieldset('lookbook_form', array('legend' => Mage::helper('lookbook')->__('Developer Configuration')));

		// Field for the title of the lookbook width
        $fieldset->addField('look_bundle_window_width', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Bundle Window Width'),
            'name'  => 'look_bundle_window_width',
			'note'  => Mage::helper('lookbook')->__('used for Lookbook Layout: grid, Click through to: Bundle product')
        ));
		
		// Field for the title of the lookbook width
        $fieldset->addField('lookbook_width', 'text', array(
            'label' => Mage::helper('lookbook')->__('Lookbook Width'),
            'name' => 'lookbook_width',
			'note'      => Mage::helper('lookbook')->__('Default to Magento theme maximum width (1120 for 1.9.1.0+, 960 for older releases)')
        ));
		
		// Field for the title of the look width
        $fieldset->addField('look_width', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Width'),
            'name' => 'look_width',
			'note'      => Mage::helper('lookbook')->__('Automatically calculated based on the looks per page and the lookbook width')
        ));
		
		// Field for the title of the look height
        $fieldset->addField('look_height', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Height'),
            'name' => 'look_height',
			'note'      => Mage::helper('lookbook')->__('Automatically calculated based on the pictures dimensions')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('look_border', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Border'),
            'name' => 'look_border',
			'note'      => Mage::helper('lookbook')->__('Default to 0')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('look_description_padding', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Description Padding'),
            'name' => 'look_description_padding',
			'note'      => Mage::helper('lookbook')->__('Default to 5')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('look_description_max_lines', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Description Maximum Lines'),
            'name' => 'look_description_max_lines',
			'note'      => Mage::helper('lookbook')->__('Default to 6')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('look_description_height', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Description Height'),
            'name' => 'look_description_height',
			'note'      => Mage::helper('lookbook')->__('Default to 90')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('look_description_border_width', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Description Border Width'),
            'name' => 'look_description_border_width',
			'note'      => Mage::helper('lookbook')->__('Default to 0')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('look_description_side_padding', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Description Side Padding'),
            'name' => 'look_description_side_padding',
			'note'      => Mage::helper('lookbook')->__('Default to 10')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('bar_width', 'text', array(
            'label' => Mage::helper('lookbook')->__('Bar Width'),
            'name' => 'bar_width',
			'note'      => Mage::helper('lookbook')->__('Default to 2')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('look_scale_height', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Scale Height'),
            'name' => 'look_scale_height',
			'note'      => Mage::helper('lookbook')->__('Default to 45')
        ));
		
		// Field for the title of the look border
        $fieldset->addField('look_scale_side_padding', 'text', array(
            'label' => Mage::helper('lookbook')->__('Look Scale Side Padding'),
            'name' => 'look_scale_side_padding',
			'note'      => Mage::helper('lookbook')->__('Default to 30')
        ));

        // Field for additional CSS
        $fieldset->addField('site_css', 'textarea', array(
            'label' => Mage::helper('lookbook')->__('Site CSS'),
            'name' => 'site_css',
        ));

        // Field for additional CSS
        $fieldset->addField('facebook_css', 'textarea', array(
            'label' => Mage::helper('lookbook')->__('Facebook CSS'),
            'name' => 'facebook_css',
        ));
		
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getLookbookData()) 
		{
            $form->setValues(Mage::getSingleton('adminhtml/session')->getLookbookData());
            Mage::getSingleton('adminhtml/session')->setLookbookData(null);
        } 
		elseif (Mage::registry('lookbook_data')) 
		{
            $form->setValues(Mage::registry('lookbook_data')->getData());
        }
	}
	
}