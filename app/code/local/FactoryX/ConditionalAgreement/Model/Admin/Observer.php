<?php

/**
 * Class FactoryX_ConditionalAgreement_Model_Admin_Observer
 */
class FactoryX_ConditionalAgreement_Model_Admin_Observer
{
    /**
     * Add the coupon chooser to the system configuration form
     * @param Varien_Event_Observer $observer
     */
    public function addFormCouponChooser(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        // Check if the form created is a system config form
        if ($block instanceof Mage_Adminhtml_Block_System_Config_Form){
            $form = $observer->getBlock()->getForm();
            // Check if the newsletter coupon fieldset is present
            if ($fieldset = $form->getElement('conditionalagreement_options'))
            {
                // Remove the original field from the system.xml
                $fieldset->removeField('conditionalagreement_options_condition');

                // Generate the salesrule chooser widget
                $config = array(
                    'input_id'    => 'conditionalagreement_options_condition',
                    'input_name'  => 'groups[options][fields][condition][value]',
                    'input_label' => 'Condition',
                    'button_text' => 'Select Sales Rule...'
                );

                $model = Mage::getModel('salesrule/rule');

                $chooserBlock = 'conditionalagreement/adminhtml_salesrule_rule_widget_chooser';
                // Create our chooser with our custom block
                Mage::helper('chooserwidget')->createChooser($model, $fieldset, $config, $chooserBlock);
            }
        }
    }

    /**
     * Editor handle is required for the chooser widget to work
     * @param Varien_Event_Observer $observer
     */
    public function addEditorHandle(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        $params = $observer->getAction()->getRequest()->getParams();
        // We add the editor handle when viewing the section where the chooser widget is displayed
        if (array_key_exists('section',$params) && $params['section'] == "conditionalagreement")
        {
            $layout->getUpdate()->addHandle('editor');
        }
    }
}