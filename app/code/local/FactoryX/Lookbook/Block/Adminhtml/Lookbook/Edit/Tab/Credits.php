<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Credits
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Credits extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the store tab for the edit lookbook page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
		
        $fieldset = $form->addFieldset('lookbook_form', array('legend' => Mage::helper('lookbook')->__('Credits Configuration')));
		
		// Field for the credits
		$showCreditsField = $fieldset->addField('show_credits', 'select', array(
            'label' => Mage::helper('lookbook')->__('Show Credits'),
            'name' => 'show_credits',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('lookbook')->__('No'),
                ),
				array(
                    'value' => 1,
                    'label' => Mage::helper('lookbook')->__('Yes'),
				)
            ),
			'note'      => Mage::helper('lookbook')->__('This will display the credits below at the bottom of the lookbook.')
		));
		
		// Field for the title of the lookbook model
        $modelField = $fieldset->addField('model', 'text', array(
            'label' => Mage::helper('lookbook')->__('Model'),
            'name' => 'model',
        ));
		
		// Field for the title of the look photograph
        $photographyField = $fieldset->addField('photography', 'text', array(
            'label' => Mage::helper('lookbook')->__('Photography'),
            'name' => 'photography',
        ));
		
		// Field for the title of the look make up
        $makeUpField = $fieldset->addField('make_up', 'text', array(
            'label' => Mage::helper('lookbook')->__('Make Up'),
            'name' => 'make_up',
        ));

        $this->setChild('form_after', $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($showCreditsField->getHtmlId(), $showCreditsField->getName())
            ->addFieldMap($modelField->getHtmlId(), $modelField->getName())
            ->addFieldMap($photographyField->getHtmlId(), $photographyField->getName())
            ->addFieldMap($makeUpField->getHtmlId(), $makeUpField->getName())
            ->addFieldDependence($modelField->getName(), $showCreditsField->getName(), 1)
            ->addFieldDependence($photographyField->getName(), $showCreditsField->getName(), 1)
            ->addFieldDependence($photographyField->getName(), $showCreditsField->getName(), 1)
        );
		
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