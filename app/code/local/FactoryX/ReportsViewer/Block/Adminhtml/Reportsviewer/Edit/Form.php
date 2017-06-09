<?php

/**
 * Class FactoryX_ReportsViewer_Block_Adminhtml_Reportsviewer_Edit_Form
 * This is the form block to view the report data
 */
class FactoryX_ReportsViewer_Block_Adminhtml_Reportsviewer_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the edit reportsviewer page
	 */
    protected function _prepareForm()
    {
        // Create a new form
        $form = new Varien_Data_Form(array(
                                        'id' => 'view_form',
                                        'method' => 'post',
										'enctype' => 'multipart/form-data'
                                     )
        );

        // Create a fieldset
        $fieldset = $form->addFieldset('reports_form', array(
                'legend'    => Mage::helper('reportsviewer')->__('General Information'),
                'class'     => 'fieldset-wide'
            )
        );

        // We retrieve the data from the registered data set in the controller
        if (Mage::registry('report_data'))
        {
            $data = Mage::registry('report_data')->getData();
        }

        // Field for the report_id of the report
        $fieldset->addField('report_id', 'text', array(
            'label' => Mage::helper('reportsviewer')->__('Report #'),
            'disabled' => true,
            'name' => 'report_id'
        ));

        // Field for the error of the report
        $fieldset->addField('error', 'textarea', array(
            'label' => Mage::helper('reportsviewer')->__('Error'),
            'disabled' => true,
            'name' => 'error'
        ));

        // Field for the trace of the report
        $fieldset->addField('trace', 'textarea', array(
            'label' => Mage::helper('reportsviewer')->__('Trace'),
            'disabled' => true,
            'name' => 'trace'
        ));

        // Field for the URL
        $fieldset->addField('url', 'text', array(
                'label' => Mage::helper('reportsviewer')->__('URL'),
                'disabled' => true,
                'name' => 'url'
            )
        );

        // Field for the skin
        $fieldset->addField('skin', 'text', array(
                'label' => Mage::helper('reportsviewer')->__('Skin'),
                'disabled' => true,
                'name' => 'skin'
            )
        );

        // Field for the script_name
        $fieldset->addField('script_name', 'text', array(
                'label' => Mage::helper('reportsviewer')->__('Script Name'),
                'disabled' => true,
                'name' => 'script_name'
            )
        );

        // Format the timestamp date to a viewable date
        $data['added'] = Mage::getModel('core/date')->date(null,$data['added']);

        // Field for the added date
        $fieldset->addField('added', 'text', array(
                'label' => Mage::helper('reportsviewer')->__('Created At'),
                'disabled' => true,
                'name' => 'added'
            )
        );

        $form->setUseContainer(true);
        // We fill the form based on the retrieved data
        $form->setValues($data);
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
	
}