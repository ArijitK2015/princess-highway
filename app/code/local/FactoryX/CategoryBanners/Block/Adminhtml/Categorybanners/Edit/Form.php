<?php

/**
 * Class FactoryX_CategoryBanners_Block_Adminhtml_Categorybanners_Edit_Form
 * This is the edit form
 */
class FactoryX_CategoryBanners_Block_Adminhtml_Categorybanners_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the edit categorybanners page
	 */
    protected function _prepareForm()
    {
        // Model registered as a banner
        Mage::registry('banner');

        // Create a form
        $form = new Varien_Data_Form(array(
                                        'id' => 'edit_form',
                                        'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                        'method' => 'post',
										'enctype' => 'multipart/form-data'
                                     )
        );
 
        $form->setUseContainer(true);
        $this->setForm($form);

        // General Information fieldset
        $fieldset = $form->addFieldset('banner_form', array(
                'legend'    => Mage::helper('categorybanners')->__('General Information'),
                //'class'     => 'fieldset-wide',
                'expanded'  => true // open
            )
        );

        // We retrieve the data from the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getBannerData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getBannerData();
            Mage::getSingleton('adminhtml/session')->setBannerData(null);
        }
        elseif (Mage::registry('banner_data'))
        {
            $data = Mage::registry('banner_data')->getData();
        }
        else $data = array();

        // Dummy values for empty dates
        if (array_key_exists('start_date',$data) && $data['start_date'] == "0000-00-00 00:00:00") $data['start_date'] = "";
        if (array_key_exists('end_date',$data) && $data['end_date'] == "0000-00-00 00:00:00") $data['end_date'] = "";

        // Add our custom type to handle the category dropdown
        $fieldset->addType('category_select','FactoryX_CategoryBanners_Model_Varien_Data_Form_Element_CategorySelect');

        // If editing an existing banner
        if (array_key_exists('category_id',$data) && $data['category_id'])
        {
            // We set the category ID as the category to be displayed in the disabled field
            $data['category_text'] = $data['category_id'];

            $fieldset->addField('category_text', 'hidden', array(
                'name' => 'category_text',
                'disabled' => true
            ));
        }

        // Display possible choices using our custom category list type
        $fieldset->addField('category_id', 'category_select', array(
            'label'     => Mage::helper('categorybanners')->__('Choose a Category'),
            'name'      => 'category_id'
        ));

        // Add our custom bannerimage type
        $fieldset->addType('bannerimage','FactoryX_CategoryBanners_Model_Varien_Data_Form_Element_BannerImage');

        // We add the picture picker using our custom bannerimage type
        $fieldset->addField('image', 'bannerimage', array(
            'label'     => Mage::helper('categorybanners')->__('Image'),
            'class'		=> 'required-entry required-file',
            'required'  => true,
            'name'      => 'image'
        ));

        // Field for the alt of the banner
        $fieldset->addField('alt', 'text', array(
            'label' => Mage::helper('categorybanners')->__('Alternative Text Image'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'alt',
        ));

        // Field for the URL
        $fieldset->addField('url', 'text', array(
                'label' => Mage::helper('categorybanners')->__('URL'),
                'name' => 'url'
            )
        );

        // List of statuses
        $statuses = Mage::getSingleton('categorybanners/status')->getOptionArray();

        // Field for the status
        $statusField = $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('categorybanners')->__('Status'),
            'name' => 'status',
            'values' => $statuses
        ));

        // Output format for the start and end dates
        $outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        // Field for the start date validated with javascript
        $startDateField = $fieldset->addField('start_date', 'date', array(
            'name' => 'start_date',
            'label' => $this->__('Start Date'),
            'title' => $this->__('Start Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'class' => 'validate-date-au',
            'note'      => Mage::helper('categorybanners')->__('Only with automatic status: banners will automatically start being displayed at 1am on the start date.')
        ));

        // Field for the end date validated with javascript
        $endDateField = $fieldset->addField('end_date', 'date', array(
            'name' => 'end_date',
            'label' => $this->__('End Date'),
            'title' => $this->__('End Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'class' => 'validate-date-au',
            'note'      => Mage::helper('categorybanners')->__('Only with automatic status: banners will automatically stop being displayed at 1am on the end date.')
        ));

        // Field for the display on children
        $fieldset->addField('display_on_children', 'select', array(
            'label' => Mage::helper('categorybanners')->__('Display on Children'),
            'name' => 'display_on_children',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('categorybanners')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('categorybanners')->__('Yes'),
                )
            )
        ));

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
                FactoryX_CategoryBanners_Model_Status::STATUS_AUTOMATIC)
            ->addFieldDependence(
                $endDateField->getName(),
                $statusField->getName(),
                FactoryX_CategoryBanners_Model_Status::STATUS_AUTOMATIC)
        );

        return parent::_prepareForm();
    }
	
}