<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_Design
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Prepare the form of the media tab for the edit homepage page
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('homepage_form', array('legend' => Mage::helper('homepage')->__('Homepage Design')));

        // We fill the form based on the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getHomepageData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getHomepageData();
        }
        elseif (Mage::registry('homepage_data'))
        {
            $data = Mage::registry('homepage_data')->getData();
        }
        else $data = array();

        if ($data['slider']) {

            // Add our custom pickable image type
            $fieldset->addType('dropdown_images', 'FactoryX_Homepage_Model_Varien_Data_Form_Element_DropdownImages');

            // Field for the slider speed
            $fieldset->addField('slider_speed', 'select', array(
                'label' => Mage::helper('homepage')->__('Slider Speed'),
                'name' => 'slider_speed',
                'values' => array(
                    array(
                        'value' => 1000,
                        'label' => Mage::helper('homepage')->__('1'),
                    ),
                    array(
                        'value' => 2000,
                        'label' => Mage::helper('homepage')->__('2'),
                    ),
                    array(
                        'value' => 3000,
                        'label' => Mage::helper('homepage')->__('3'),
                    ),
                    array(
                        'value' => 5000,
                        'label' => Mage::helper('homepage')->__('5'),
                    ),
                    array(
                        'value' => 10000,
                        'label' => Mage::helper('homepage')->__('10'),
                    )
                ),
                'note' => Mage::helper('homepage')->__('In seconds.')
            ));

            // Field for the slider speed
            $fieldset->addField('slider_direction', 'select', array(
                'label' => Mage::helper('homepage')->__('Slider Direction'),
                'name' => 'slider_direction',
                'values' => array(
                    array(
                        'value' => 'both',
                        'label' => Mage::helper('homepage')->__('Both'),
                    ),
                    array(
                        'value' => 'vertical',
                        'label' => Mage::helper('homepage')->__('Vertical'),
                    ),
                    array(
                        'value' => 'horizontal',
                        'label' => Mage::helper('homepage')->__('Horizontal'),
                    ),
                    array(
                        'value' => 'none',
                        'label' => Mage::helper('homepage')->__('None'),
                    )
                )
            ));

            // Get corresponding folder
            $folder = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default')) . '/images/factoryx/homepage';;
            $subfolder = 'nav';

            // Get list of corresponding pictures
            $navPix = Mage::helper('homepage')->dirFiles($folder, $subfolder);

            // Field for the slider navigation style
            $fieldset->addField('slider_nav_dropdown', 'dropdown_images', array(
                'label' => Mage::helper('homepage')->__('Slider Navigation Style'),
                'name' => 'slider_nav_dropdown',
                'values' => $navPix
            ));

            $fieldset->addField('slider_nav_style', 'hidden', array(
                'name' => 'slider_nav_style'
            ));

            // Get corresponding folder
            $subfolder = 'pag';

            // Get list of corresponding pictures
            $pagPix = Mage::helper('homepage')->dirFiles($folder, $subfolder);

            // Field for the slider pagination style
            $fieldset->addField('slider_pagination_dropdown', 'dropdown_images', array(
                'label' => Mage::helper('homepage')->__('Slider Pagination Style'),
                'name' => 'slider_pagination_dropdown',
                'values' => $pagPix
            ));

            $fieldset->addField('slider_pagination_style', 'hidden', array(
                'name' => 'slider_pagination_style'
            ));
        }

        // We add the picture picker using our custom homepageimage type
        $fieldset->addField('custom_css', 'textarea', array(
            'label'     => Mage::helper('homepage')->__('Custom CSS'),
            'name'      => 'custom_css'
        ));

        // We fill the form based on the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getHomepageData())
        {
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setHomepageData(null);
        }
        elseif (Mage::registry('homepage_data'))
        {
            $form->setValues($data);
        }
    }

}