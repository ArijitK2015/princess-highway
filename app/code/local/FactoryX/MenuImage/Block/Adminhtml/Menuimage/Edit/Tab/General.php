<?php

/**
 * Class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Edit_Tab_General
 */
class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the general tab to edit a menuimage
     */
    protected function _prepareForm()
    {
        // Model registered as a menuimage
        Mage::registry('menuimage');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('menuimage_form', array('legend' => Mage::helper('menuimage')->__('General Information')));

        // We retrieve the data from the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getMenuimageData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getMenuimageData();
            Mage::getSingleton('adminhtml/session')->setMenuimageData(null);
        }
        elseif (Mage::registry('menuimage_data'))
        {
            $data = Mage::registry('menuimage_data')->getData();
        }
        else $data = array();

        // We add the blocks data to the original data array
        $data = Mage::helper('menuimage')->addBlocksData($data);

        /*
         * @deprecated from 0.2.0
        // Field for the category id
        // Add our custom pickable image type
        $fieldset->addType('category_select','FactoryX_MenuImage_Model_Varien_Data_Form_Element_CategorySelect');

        if (array_key_exists('category_id',$data) && $data['category_id'])
        {
            $data['category_text'] = $data['category_id'];

            $fieldset->addField('category_text', 'hidden', array(
                'name' => 'category_text',
                'disabled' => true
            ));
        }

        // Display possible choices using our custom pickable image type
        $fieldset->addField('category_id', 'category_select', array(
            'label'     => Mage::helper('menuimage')->__('Choose a Category'),
            'name'      => 'category_id'
        ));
        */

        // Generate the category chooser widget
        $config = array(
            'input_id'    => 'category_id',
            'input_name'  => 'category_id',
            'input_label' => Mage::helper('menuimage')->__('Choose a Category'),
            'button_text' => 'Select Category...'
        );

        $model = Mage::getModel('catalog/category');
        if (array_key_exists('category_id',$data)
            && $data['category_id'])
        {
            if (strpos("category/",$data['category_id']) === false) {
                $data['category_id'] = "category/" . $data['category_id'];
            }
            $model->setData(array('category_id' => $data['category_id']));
            unset($data['category_id']);
        }

        // Create a category chooser
        Mage::helper('chooserwidget')->createCategoryChooser($model, $fieldset, $config);

        // Field for the status (Enabled or not)
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('menuimage')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('menuimage')->__('Disabled'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('menuimage')->__('Enabled'),
                )
            )
        ));

        for ($i = 1; $i <= Mage::helper('menuimage')->getBlockCount(); $i++)
        {
            $fieldset = $form->addFieldset('menuimage_form'.$i, array('legend' => Mage::helper('menuimage')->__('Menu Image Block # %s',$i)));

            // Field to show a block
            $fieldset->addField('type_'.$i, 'select', array(
                'label' => Mage::helper('menuimage')->__('Block Type #%s',$i),
                'name' => 'type_'.$i,
                'values' => array(
                    array(
                        'value' => 0,
                        'label' => Mage::helper('menuimage')->__('None'),
                    ),
                    array(
                        'value' => 'image',
                        'label' => Mage::helper('menuimage')->__('Image'),
                    ),
                    array(
                        'value' => 'product',
                        'label' => Mage::helper('menuimage')->__('Product'),
                    )
                )
            ));

            // Field for the image
            // Add our custom menuimageimage type
            $fieldset->addType('menuimageimage','FactoryX_MenuImage_Model_Varien_Data_Form_Element_MenuimageImage');

            // We add the picture picker using our custom menuimageimage type
            $fieldset->addField('image_'.$i, 'menuimageimage', array(
                'label'     => Mage::helper('menuimage')->__('Menu Image %s',$i),
                'class'		=> 'required-file',
                'name'      => 'image_'.$i
            ));

            // We add the picture link
            $fieldset->addField('link_'.$i, 'text', array(
                'label' => Mage::helper('menuimage')->__('Menu Image Link %s',$i),
                'name' => 'link_'.$i,
            ));

            // We add the picture alt title
            $fieldset->addField('alt_'.$i, 'text', array(
                'label' => Mage::helper('menuimage')->__('Menu Image Title %s',$i),
                'name' => 'alt_'.$i,
            ));

            /*
             * @deprecated from 0.1.11
            // We add the product id
            $fieldset->addField('product_id_'.$i, 'text', array(
                'label' => Mage::helper('menuimage')->__('Product ID %s',$i),
                'name' => 'product_id_'.$i
            ));
            */

            // Generate the product chooser widget
            $config = array(
                'input_id'    => 'product_id_'.$i,
                'input_name'  => 'product_id_'.$i,
                'input_label' => Mage::helper('menuimage')->__('Product ID %s',$i),
                'button_text' => 'Select Product...'
            );

            $model = Mage::getModel('catalog/product');
            if (array_key_exists('type_'.$i,$data)
                && $data['type_'.$i] == "product"
                && $data['product_id_'.$i])
            {
                $model->setData(array('product_id_'.$i => $data['product_id_'.$i]));
                unset($data['product_id_'.$i]);
            }

            // Create a product chooser
            Mage::helper('chooserwidget')->createProductChooser($model, $fieldset, $config);

            // We add the sort_order
            $fieldset->addField('sort_order_'.$i, 'text', array(
                'label' => Mage::helper('menuimage')->__('Sort Order %s',$i),
                'name' => 'sort_order_'.$i
            ));
        }

        // Dynamic dependencies mapping
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence'));

        for ($i = 1; $i <= Mage::helper('menuimage')->getBlockCount(); $i++) {

            $this->getChild('form_after')
                ->addFieldMap('type_'.$i, 'type_'.$i)
                ->addFieldMap('link_'.$i, 'link_'.$i)
                ->addFieldMap('alt_'.$i, 'alt_'.$i)
                ->addFieldMap('image_'.$i, 'image_'.$i)
                ->addFieldMap('sort_order_'.$i, 'sort_order_'.$i)
                //->addFieldMap('product_id_'.$i, 'product_id_'.$i)
                ->addFieldDependence('link_'.$i, 'type_'.$i, 'image')
                ->addFieldDependence('image_'.$i, 'type_'.$i, 'image')
                ->addFieldDependence('alt_'.$i, 'type_'.$i, 'image')
                ->addFieldDependence('sort_order_'.$i, 'type_'.$i, 'image')
                /*->addFieldDependence('product_id_'.$i, 'type_'.$i, 'product')*/;
        }

        // We fill the form based on the retrieved data
        $form->setValues($data);

        return parent::_prepareForm();
    }

}