<?php

/**
 * Class FactoryX_Instagram_Block_Adminhtml_Instagram_Edit_Tab_General
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the general tab to edit a instagram list
     */
    protected function _prepareForm()
    {
        // Model registered as a instagramlist
        Mage::registry('instagramlist');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        // General Information
        $fieldset = $form->addFieldset('instagramlist_form', array(
                'legend'    => Mage::helper('instagram')->__('General Information'),
                //'class'     => 'fieldset-wide',
                'expanded'  => true // open
            )
        );

        // Field for the tags of the banner
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('instagram')->__('Title'),
            'name' => 'title'
        ));

        // Field for the tags of the banner
        $fieldset->addField('link', 'text', array(
            'label' => Mage::helper('instagram')->__('URL'),
            'name' => 'link',
            'note' => Mage::helper('instagram')->__('Only used when displayed as widget (e.g. on the homepage)')
        ));

        // We retrieve the data from the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getInstagramlistData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getInstagramlistData();
            Mage::getSingleton('adminhtml/session')->setInstagramlistData(null);
        }
        elseif (Mage::registry('instagramlist_data'))
        {
            $data = Mage::registry('instagramlist_data')->getData();
        }
        else $data = array();

        // List of types
        $types = Mage::getSingleton('instagram/instagramlist')->getTypesOptionArray();

        $updateTypeField = $fieldset->addField('updatetype', 'select', array(
            'label'     => Mage::helper('instagram')->__('Choose an Update Type'),
            'name'      => 'updatetype',
            'values'    => $types
        ));

        // Field for the tags of the banner
        $tagsField = $fieldset->addField('tags', 'textarea', array(
            'label' => Mage::helper('instagram')->__('Instagram Hashtags'),
            'name' => 'tags',
            'note' => Mage::helper('instagram')->__('Comma-separated list of hashtags')
        ));

        // Field for the tags of the banner
        $usersField = $fieldset->addField('users', 'textarea', array(
            'label' => Mage::helper('instagram')->__('Instagram Users'),
            'name' => 'users',
            'note' => Mage::helper('instagram')->__('Comma-separated list of users ids')
        ));

        // Field for the tags of the banner
        $fieldset->addField('image_size', 'text', array(
            'label' => Mage::helper('instagram')->__('Image size'),
            'name' => 'image_size',
            'note' => Mage::helper('instagram')->__('Default 237 (4 per row)')
        ));

        // Field for the tags of the banner
        $fieldset->addField('style', 'textarea', array(
            'label' => Mage::helper('instagram')->__('Style'),
            'name' => 'style',
            'note' => Mage::helper('instagram')->__('Extra CSS')
        ));

        // Field for the tags of the banner
        $fieldset->addField('limit', 'text', array(
            'label' => Mage::helper('instagram')->__('Limit'),
            'name' => 'limit',
            'note' => Mage::helper('instagram')->__('Limit the collection to X items, useful for widget e.g. on homepage')
        ));

        // Field for the tags of the banner
        $fieldset->addField('show_per_page', 'text', array(
            'label' => Mage::helper('instagram')->__('Show Per Page'),
            'name' => 'show_per_page',
            'note' => Mage::helper('instagram')->__('Only used when displayed as list')
        ));

        // Field for the display on children
        $fieldset->addField('hover', 'select', array(
            'label' => Mage::helper('instagram')->__('Show caption on hover only'),
            'name' => 'hover',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('instagram')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('instagram')->__('Yes'),
                )
            )
        ));

        // Field for the display on children
        $fieldset->addField('display_likes', 'select', array(
            'label' => Mage::helper('instagram')->__('Display number of likes in caption'),
            'name' => 'display_likes',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('instagram')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('instagram')->__('Yes'),
                )
            )
        ));

        // Field for the display on children
        $fieldset->addField('strip_caption', 'select', array(
            'label' => Mage::helper('instagram')->__('Strip Caption'),
            'name' => 'strip_caption',
            'values' => array(
                array(
                    'value' => 'full',
                    'label' => Mage::helper('instagram')->__('Keep both text and tags'),
                ),
                array(
                    'value' => 'text',
                    'label' => Mage::helper('instagram')->__('Only keep text'),
                ),
                array(
                    'value' => 'tags',
                    'label' => Mage::helper('instagram')->__('Only keep tags'),
                )
            )
        ));

        // Field for the layout mode
        $fieldset->addField('layout_mode', 'select', array(
            'label' => Mage::helper('instagram')->__('Layout Mode'),
            'name' => 'layout_mode',
            'values' => array(
                array(
                    'value' => 'masonry',
                    'label' => Mage::helper('instagram')->__('Masonry'),
                ),
                array(
                    'value' => 'fitRows',
                    'label' => Mage::helper('instagram')->__('Fit Rows'),
                ),
                array(
                    'value' => 'vertical',
                    'label' => Mage::helper('instagram')->__('Vertical'),
                )
            ),
            'note' => Mage::helper('instagram')->__('More details at this address: http://isotope.metafizzy.co/layout-modes.html')
        ));

        // We fill the form based on the retrieved data
        $form->setValues($data);

        // Add dynamic dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($updateTypeField->getHtmlId(), $updateTypeField->getName())
            ->addFieldMap($tagsField->getHtmlId(), $tagsField->getName())
            ->addFieldMap($usersField->getHtmlId(), $usersField->getName())
            ->addFieldDependence(
                $tagsField->getName(),
                $updateTypeField->getName(),
                FactoryX_Instagram_Model_Instagramlist::UPDATE_TYPE_TAG)
            ->addFieldDependence(
                $usersField->getName(),
                $updateTypeField->getName(),
                FactoryX_Instagram_Model_Instagramlist::UPDATE_TYPE_USER)
        );

        return parent::_prepareForm();
    }

}