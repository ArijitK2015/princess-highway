<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Email
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Email extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_exampleEmailPictureUrl = "http://shop.alannahhill.com.au/skin/frontend/default/theme010k/images/raf/bagpromoemail.jpg";
    
    /**
     * Prepare the form of the email tab for the edit contest page
     */
    protected function _prepareForm() 
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /**
        Confirmation Email
        */
        $fieldsetConfirmationEmail = $form->addFieldset('contests_form_confirmation_email', array('legend' => Mage::helper('contests')->__('Confirmation Email')));

        // Field to enable the facebook sharing
        $confirmationField = $fieldsetConfirmationEmail->addField('confirmation_email', 'select', array(
            'label' => Mage::helper('contests')->__('Send Confirmation Email'),
            'name' => 'confirmation_email',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Yes'),
                )
            )
        ));

        // Field to enable the facebook sharing
        $confirmationTemplateField = $fieldsetConfirmationEmail->addField('confirmation_email_template_id', 'select', array(
            'label' => Mage::helper('contests')->__('Confirmation Email Template'),
            'name' => 'confirmation_email_template_id',
            'values' => Mage::helper('contests')->getTransactionalEmailList()
        ));

        // Field for email template subject
        $confirmationSubjectField = $fieldsetConfirmationEmail->addField('email_subject', 'text', array(
            'label' => Mage::helper('contests')->__('Confirmation Email Subject'),
            'name' => 'email_subject',
        ));

        // Add dynamic dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($confirmationField->getHtmlId(), $confirmationField->getName())
            ->addFieldMap($confirmationTemplateField->getHtmlId(), $confirmationTemplateField->getName())
            ->addFieldMap($confirmationSubjectField->getHtmlId(), $confirmationSubjectField->getName())
            ->addFieldDependence(
                $confirmationTemplateField->getName(),
                $confirmationField->getName(),
                1)
            ->addFieldDependence(
                $confirmationSubjectField->getName(),
                $confirmationField->getName(),
                1)
        );

        /**
        Referral Email
        */
        $fieldsetReferralEmail = $form->addFieldset('contests_form_referral_email', array('legend' => Mage::helper('contests')->__('Referral Email')));

        // We use our own image type to handle the pictures properly
        $fieldsetReferralEmail->addType('contestimage','FactoryX_Contests_Model_Varien_Data_Form_Element_ContestImage');

        // Field to upload the email image related to the contest
        $fieldsetReferralEmail->addField('email_image_url', 'contestimage', array(
            'label'     => Mage::helper('contests')->__('Referral Email Picture'),
            'name'      => 'email_image_url',
            'note'      => Mage::helper('contests')->__('Only for refer a friend contest, recommended image size: 980px wide and any height. (<a target="_blank" rel="noopener noreferrer" href="%s">example</a>)',$this->_exampleEmailPictureUrl),
        ));

        // Email template
        $referralEmailTemplateField = $fieldsetReferralEmail->addField('email_template_id', 'select', array(
            'label' => Mage::helper('contests')->__('Referral Email Template'),
            'name' => 'email_template_id',
            'values' => Mage::helper('contests')->getTransactionalEmailList()
        ));

        // Field for email template subject
        $referralEmailSubjectField = $fieldsetReferralEmail->addField('referee_email_subject', 'text', array(
            'label' => Mage::helper('contests')->__('Referee Email Subject'),
            'name' => 'referee_email_subject',
        ));
        
        // We fill the form based on the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getContestsData())  {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContestsData());
            Mage::getSingleton('adminhtml/session')->setContestsData(null);
        } 
        elseif (Mage::registry('contests_data')) {
            $form->setValues(Mage::registry('contests_data')->getData());
        }
    }
}