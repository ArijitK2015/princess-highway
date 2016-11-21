<?php

/**
 * Class FactoryX_Lookbook_Model_Varien_Data_Form_Element_ImageGallery
 */
class FactoryX_Lookbook_Model_Varien_Data_Form_Element_ImageGallery extends Varien_Data_Form_Element_Image
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getContentHtml();
        return $html;
    }

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {

        /* @var $content FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Media_Gallery_Content */
        $content = Mage::getSingleton('core/layout')
            ->createBlock('FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Media_Gallery_Content');

        $content->setId($this->getHtmlId() . '_content')
            ->setElement($this);
        return $content->toHtml();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return '';
    }

    /**
     * Retrieve data object related with form
     *
     * @return Mage_Catalog_Model_Product || Mage_Catalog_Model_Category
     */
    public function getDataObject()
    {
        return $this->getForm()->getDataObject();
    }

    /**
     * Retrieve attribute field name
     *
     *
     * @param array $attribute ( code => label )
     * @return string
     */
    public function getAttributeFieldName($attribute)
    {
        $name = key($attribute);
        if ($suffix = $this->getForm()->getFieldNameSuffix()) {
            $name = $this->getForm()->addSuffixToName($name, $suffix);
        }
        return $name;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return '<tr><td class="value" colspan="3">' . $this->getElementHtml() . '</td></tr>';
    }
}