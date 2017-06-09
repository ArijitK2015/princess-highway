<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_General
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the general tab to edit a homepage
     */
    protected function _prepareForm()
    {
        // Model registered as a homepage
        Mage::registry('homepage');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('homepage_form', array('legend' => Mage::helper('homepage')->__('General Information')));

        // We retrieve the data from the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getHomepageData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getHomepageData();
            Mage::getSingleton('adminhtml/session')->setHomepageData(null);
        }
        elseif (Mage::registry('homepage_data'))
        {
            $data = Mage::registry('homepage_data')->getData();
        }
        else $data = array();

        // Dummy values for disabled field
        $data['amount_text'] = $data['amount'];
        $data['slider_text'] = $data['slider'];

        // Dummy values for empty dates
        if (array_key_exists('start_date',$data) && $data['start_date'] == "0000-00-00 00:00:00") $data['start_date'] = "";
        if (array_key_exists('end_date',$data) && $data['end_date'] == "0000-00-00 00:00:00") $data['end_date'] = "";

        // Field for the title of the homepage
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('homepage')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        // List of statuses
        $statuses = Mage::getSingleton('homepage/status')->getOptionArray();

        // Field for the status (Enabled or not)
        $statusField = $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('homepage')->__('Status'),
            'name' => 'status',
            'values' => $statuses,
            'note'      => Mage::helper('homepage')->__('The automatic status will use start and end dates to automatically enable and disable the homepage.')
        ));

        // Output format for the start and end dates
        $outputFormat = "dd/MM/yyyy";

        // Field for the start date validated with javascript
        $startDateField = $fieldset->addField('start_date', 'date', array(
            'name' => 'start_date',
            'label' => $this->__('Start Date'),
            'title' => $this->__('Start Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'class' => 'validate-date-au',
            'note'      => Mage::helper('homepage')->__('Only with automatic status: homepage will automatically start at 1am on the start date.')
        ));

        // Field for the end date validated with javascript
        $endDateField = $fieldset->addField('end_date', 'date', array(
            'name' => 'end_date',
            'label' => $this->__('End Date'),
            'title' => $this->__('End Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'class' => 'validate-date-au',
            'note'      => Mage::helper('homepage')->__('Only with automatic status: homepage will automatically end at 1am on the end date.')
        ));

        $fieldset->addField('amount_text', 'text', array(
            'label' => Mage::helper('homepage')->__('Number of pictures'),
            'name' => 'amount_text',
            'disabled' => true
        ));

        $fieldset->addField('amount', 'hidden', array(
            'name'	=> 'amount'
        ));

        $fieldset->addField('slider_text', 'select', array(
            'label' => Mage::helper('homepage')->__('Slider'),
            'name' => 'slider_text',
            'disabled' => true,
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
        ));

        $fieldset->addField('slider', 'hidden', array(
            'name'	=> 'slider'
        ));

        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('homepage')->__('Sort Order'),
            'name' => 'sort_order'
        ));

        if (array_key_exists('themes',$data))
        {
            $data['themes'] = explode(',',$data['themes']);
        }

        // Get available designs
        $availableDesigns = Mage::helper('homepage')->getAllowedThemes();

        // Add an all design
        if(!empty($availableDesigns)) {
            array_unshift($availableDesigns, array(
                'label' => $this->__('All Designs'),
                'value' => 'all'
            ));
        }

        // Field for the theme picker
        $fieldset->addField('themes','multiselect',array(
            'name'   => 'themes[]',
            'label' => $this->__('Theme'),
            'title' => $this->__('Theme'),
            'values'    => $availableDesigns
        ));

        // List of Full Width

        // Field for the Full Width (Yes or no)
        $fw_statusField = $fieldset->addField('full_width', 'select', array(
            'label' => Mage::helper('homepage')->__('Full Width'),
            'name' => 'full_width',
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
            'note'      => Mage::helper('homepage')->__('set if the homepage is put in the full width position.')
            )
        );


        // We fill the form based on the retrieved data
        $form->setValues($data);

        // Add dynamic dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($statusField->getHtmlId(), $statusField->getName())
            ->addFieldMap($startDateField->getHtmlId(), $startDateField->getName())
            ->addFieldMap($endDateField->getHtmlId(), $endDateField->getName())
            ->addFieldDependence(
                $startDateField->getName(),
                $statusField->getName(),
                FactoryX_Homepage_Model_Status::STATUS_AUTOMATIC)
            ->addFieldDependence(
                $endDateField->getName(),
                $statusField->getName(),
                FactoryX_Homepage_Model_Status::STATUS_AUTOMATIC)
        );

        return parent::_prepareForm();
    }

}