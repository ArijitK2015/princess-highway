<?php

/**
 * Class FactoryX_ImageCdn_Block_Adminhtml_Cachedb_Edit_Form
 * This is the edit form
 */
class FactoryX_ImageCdn_Block_Adminhtml_Cachedb_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the edit categorybanners page
	 */
    protected function _prepareForm()
    {
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
        $fieldset = $form->addFieldset('warm_form', array(
                'legend'    => Mage::helper('imagecdn')->__('General Information'),
                //'class'     => 'fieldset-wide',
                'expanded'  => true // open
            )
        );

        // Field for the type of the images
        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('imagecdn')->__('Image Type'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'type',
            'values' => array(
                array(
                    'value' => 'thumbnail',
                    'label' => Mage::helper('imagecdn')->__('Thumbnail')
                ),
                array(
                    'value' => 'small_image',
                    'label' => Mage::helper('imagecdn')->__('Small Image')
                ),
                array(
                    'value' => 'image',
                    'label' => Mage::helper('imagecdn')->__('Image')
                )
            )
        ));

        // Field for the size
        $fieldset->addField('size', 'text', array(
                'label' => Mage::helper('imagecdn')->__('Size'),
                'name' => 'size',
                'class' => 'required-entry',
                'required' => true,
                'note'      => Mage::helper('imagecdn')->__('E.g. 200x200')
            )
        );

        // Field for the size
        $fieldset->addField('folder', 'text', array(
                'label' => Mage::helper('imagecdn')->__('Folder'),
                'name' => 'folder',
                'class' => 'required-entry',
                'required' => true
            )
        );

        // Field for the size
        $fieldset->addField('limit', 'text', array(
                'label' => Mage::helper('imagecdn')->__('Limit'),
                'name' => 'limit'
            )
        );

        // Field for the size
        $fieldset->addField('filter', 'text', array(
                'label' => Mage::helper('imagecdn')->__('Filter'),
                'name' => 'filter'
            )
        );

        return parent::_prepareForm();
    }
	
}