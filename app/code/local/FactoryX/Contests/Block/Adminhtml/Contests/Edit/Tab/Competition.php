<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Competition extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the competition tab for the edit contest page
     */
    protected function _prepareForm() 
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contests_form', array('legend' => Mage::helper('contests')->__('Competition Configuration')));
        
        // Field to choose if the contest is a competition or not
        $fieldset->addField('is_competition', 'select', array(
            'label' => Mage::helper('contests')->__('Is A Competition Contest ?'),
            'name' => 'is_competition',
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
        
        // Field for the competition text
        $fieldset->addField('competition_text', 'text', array(
            'label' => Mage::helper('contests')->__('Competition Text'),
            'name' => 'competition_text',
            'note' => Mage::helper('contests')->__('Text to display as the question.')
        ));

        // Fields for competition options (csv)
        $fieldset->addField('competition_options', 'text', array(
            'label' => Mage::helper('contests')->__('Competition Options'),
            'name' => 'competition_options',
            'note' => Mage::helper('contests')->__('Enter the competition options, seperated by comma."')
        ));
        
        // Field for the minimum word of a competition
        $fieldset->addField('maximum_word_count', 'text', array(
            'label' => Mage::helper('contests')->__('Maximum Word Count'),
            'name' => 'maximum_word_count'
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