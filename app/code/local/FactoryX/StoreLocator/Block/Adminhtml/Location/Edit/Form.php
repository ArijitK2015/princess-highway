<?php
/**
 * FactoryX_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @copyright
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @author
 */
class FactoryX_StoreLocator_Block_Adminhtml_Location_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $hlp = Mage::helper('ustorelocator');

        $data = array();
        if (Mage::registry('location_data')) {
            $data = Mage::registry('location_data')->getData();
        }

        $form = new Varien_Data_Form(array(
                                        'id' => 'edit_form',
                                        'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                        'method' => 'post',
                                        'enctype'=> 'multipart/form-data'
                                     )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('location_form', array(
            'legend'=>$hlp->__('Store Location Info')
        ));

        $fieldset->addField('store_code', 'text', array(
            'name'      => 'store_code',
            'label'     => $hlp->__('Store Code'),
            'class'     => 'required-entry',
            'required'  => true,
        ));

        $fieldset->addField('store_type', 'select', array(
            'name'      => 'store_type',
            'label'     => $hlp->__('Store Type'),
            'class'     => 'required-entry',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 'standalone',
                    'label'     => 'Standalone',
                ),
                array(
                    'value'     => 'myer',
                    'label'     => 'Concession - Myer',
                ),
                array(
                    'value'     => 'djs',
                    'label'     => 'Concession - David Jones',
                ),
                array(
                    'value'     => 'clearance',
                    'label'     => 'Clearance'
                ),
                array(
                    'value'     => 'retailer',
                    'label'     => 'Retailer'
                ),                
                array(
                    'value'     => 'online_only',
                    'label'     => 'Online Only'
                )                
            )
        ));

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => $hlp->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
        ));

        $fieldset->addField('address_display', 'textarea', array(
            'name'      => 'address_display',
            'label'     => $hlp->__('Street Address'),
            'class'     => 'required-entry',
            'style'     => 'height:50px',
            'required'  => true,
        ));

        $fieldset->addField('postcode', 'text', array(
            'name'      => 'postcode',
            'label'     => $hlp->__('Postcode'),
            'class'     => 'required-entry',
            'required'  => true,
        ));

        $fieldset->addField('suburb', 'text', array(
            'name'      => 'suburb',
            'label'     => $hlp->__('Suburb'),
            'class'     => 'required-entry',
            'required'  => true,
        ));

        $fieldset->addField('region', 'select', array(
            'name'      => 'region',
            'label'     => Mage::helper('ustorelocator')->__('Region'),
            'class'     => 'required-entry',
            'required'  => true,
            'values'    => $hlp->getRegionsToOptionArray(true)
        ));

        // We use our own image type to handle the pictures properly
        $fieldset->addType('storeimage','FactoryX_StoreLocator_Model_Varien_Data_Form_Element_StoreImage');

        // Field to upload an image related to the store
        $fieldset->addField('image', 'storeimage', array(
            'label'     => $hlp->__('Store Image'),
            'class'     => 'required-file',
            'name'      => 'image'
        ));

        $fieldset->addField('phone', 'text', array(
            'name'      => 'phone',
            'label'     => $hlp->__('Phone'),
        ));

        $fieldset->addField('website_url', 'text', array(
            'name'      => 'website_url',
            'label'     => $hlp->__('Website URL / Email'),
            'note'      => $hlp->__('For websites URL please start with http://'),
        ));

        $fieldset->addField('ip_address', 'text', array(
            'name'      => 'ip_address',
            'label'     => $hlp->__('Store IP Address'),
            'note'      => $hlp->__('This is loaded automatcically via a cron job'),
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $values = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false);
            $fieldset->addField('stores', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'values'    => $values,
            ));
        }

        $fieldset->addField(
            'country', 'select',
            array(
                'name'  => 'country',
                'label' => $hlp->__("Select location country"),
                'values'=> Mage::getModel('adminhtml/system_config_source_country')->toOptionArray()
            ));

        /*
                $fieldset->addField(
                    'product_types', 'text',
                    array(
                         'name' => 'product_types',
                         'label'=> $hlp->__("Store type"),
                         'note' => $hlp->__("Comma separated list of product types sold on this location.")
                ));
        */

        $fieldset->addField(
            'is_featured',
            'select',
            array(
                'name'  => 'is_featured',
                'label' => $hlp->__("This is featured location"),
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
            )
        );

        $fieldset->addField('notes', 'textarea', array(
            'name'      => 'notes',
            'style'     => 'height:50px',
            'label'     => $hlp->__('Notes'),
        ));

        $fieldset = $form->addFieldset(
            'map_settings',
            array(
                'legend' => $hlp->__("Map settings")
            )
        );
        $fieldset->addField(
            'icon',
            'image',
            array(
                'name'  => 'icon',
                'label' => $hlp->__("Custom icon image"),
                'note'  => $hlp->__("Allowed file type: <strong>PNG</strong>.<br/>For best quality provide image with dimensions close to default Google icons - width 20px, height 34px.<br/>Maximum allowed size <strong>100px by 100px</strong>")
            )
        );

        $fieldset->addField(
            'use_label',
            'select',
            array(
                'label' => $hlp->__("Add sequence label to marker?"),
                'name'  => 'use_label',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note'  => $this->__("This setting is used only when no custom icon is provided.")
            )
        );

        $fieldset = $form->addFieldset('geo_form', array(
            'legend'=>$hlp->__('Geo Location')
        ));

        $fieldset->addField('address', 'textarea', array(
            'name'      => 'address',
            'style'     => 'height:50px',
            'label'     => $hlp->__('Address for geo location'),
            'note'      => $hlp->__('This address will be used to calculate latitude and longitude, free format is allowed.<br/>If left empty, will be copied from address to be displayed.'),
        ));

        $fieldset->addField('latitude', 'text', array(
            'name'      => 'latitude',
            'label'     => $hlp->__('Latitude'),
        ));

        $fieldset->addField('longitude', 'text', array(
            'name'      => 'longitude',
            'label'     => $hlp->__('Longitude'),
            'note'      => $hlp->__('If empty, will attempt to retrieve using the geo location address.'),
        ));


        $fieldset->addField(
            'zoom',
            'text',
            array(
                'name' => 'zoom',
                'label' => $hlp->__("Initial location zoom"),
                'note'  => $hlp->__("A number between 1 and 25, where 1 is max zoomed out and 25 is closest zoom possible.")
            )
        );

        $fieldsetCustomFields = $form->addFieldset('custom_fields', array(
            'legend'=>$hlp->__('Custom Fields')
        ));

        $fieldsetCustomFields->addField('custom1', 'text', array(
            'name'      => 'data_serialized[custom1]',
            'label'     => $hlp->__('Custom field 1'),
        ));

        $fieldsetCustomFields->addField('custom2', 'text', array(
            'name'      => 'data_serialized[custom2]',
            'label'     => $hlp->__('Custom field 2'),
        ));

        $fieldsetCustomFields->addField('custom3', 'text', array(
            'name'      => 'data_serialized[custom3]',
            'label'     => $hlp->__('Custom field 3'),
        ));

        $fieldset = $form->addFieldset('label', array(
            'legend'=>$hlp->__('Aupost Labels')
        ));


        if (class_exists('FactoryX_ShippedFrom_Model_System_Config_Source_LabelLayouts')) {
            $fieldset->addField('label_layout', 'select', array(
                'name' => 'label_layout',
                'label' => $hlp->__("Label Layout"),
                'values' => Mage::getModel('shippedfrom/system_config_source_labelLayouts')->toOptionArray()
            ));
        }

        $fieldset->addField('label_branded', 'select', array(
            'name'  => 'label_branded',
            'label' => $hlp->__("Is Branded ?"),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        $fieldset->addField('label_left_offset', 'text', array(
            'name'  => 'label_left_offset',
            'label' => $hlp->__("Label left offset")
        ));

        $fieldset->addField('label_top_offset', 'text', array(
            'name'  => 'label_top_offset',
            'label' => $hlp->__("Label left offset")
        ));

        Mage::dispatchEvent('ustorelocator_adminhtml_edit_prepare_form', array('block'=>$this, 'form'=>$form));

        if (Mage::registry('location_data')) {
            if (isset($data['data_serialized']) && !empty($data['data_serialized'])) {
                $data = array_merge($data, json_decode($data['data_serialized'], true));
            }
            $form->setValues($data);
        }

        return parent::_prepareForm();
    }
}