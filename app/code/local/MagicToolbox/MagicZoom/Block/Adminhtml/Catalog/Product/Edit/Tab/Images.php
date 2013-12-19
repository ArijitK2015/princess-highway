<?php

class MagicToolbox_MagicZoom_Block_Adminhtml_Catalog_Product_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('group_fields_magiczoom_images', array('legend' => Mage::helper('magiczoom')->__('Images'), 'class' => 'magiczoomFieldset'));
        $fieldset->addType('magiczoom_gallery', 'MagicToolbox_MagicZoom_Block_Adminhtml_Settings_Edit_Tab_Form_Element_Gallery');
        $fieldset->addField('magiczoom_gallery', 'magiczoom_gallery', array(
            'label'     => Mage::helper('magiczoom')->__('${too.id} gallery'),
            'name'      => 'magiczoom[gallery]',
        ));
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel() {
        return $this->__('Magic Zoom&#8482; Images');
    }

    public function getTabTitle() {
        return $this->__('Magic Zoom&#8482; Images');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getHtmlId() {
        return $this->getId();
    }

    public function getJsObjectName() {
        return $this->getHtmlId().'JsObject';
    }

}

