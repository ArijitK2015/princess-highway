<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_SocialMedia
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_SocialMedia extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the general tab to edit a lookbook
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        // Social Media
        $fieldset = $form->addFieldset('lookbook_form', array('legend' => Mage::helper('lookbook')->__('Social Media')));

        // Will this lookbook be used on Facebook?
        $afterElementHtml = sprintf("<p class=\"note\"><span>see Facebook view action in grid for the app url</span></p>");
        
        $fieldset->addField('lookbook_facebook', 'select', array(
            'label'     => Mage::helper('lookbook')->__('Will this be used on Facebook'),
            'name'      => 'lookbook_facebook',
            'values'    => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('lookbook')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('lookbook')->__('Yes'),
                )
            ),
            'after_element_html' => $afterElementHtml
        ));

        // Field for facebook app id
        $fieldset->addField('facebook_app_id', 'text', array(
            'label' => Mage::helper('lookbook')->__('Facebook App Id'),
            'name' => 'facebook_app_id',
        ));

        // Field for facebook app id
        $fieldset->addField('facebook_app_secret', 'text', array(
            'label' => Mage::helper('lookbook')->__('Facebook App Secret'),
            'name' => 'facebook_app_secret',
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