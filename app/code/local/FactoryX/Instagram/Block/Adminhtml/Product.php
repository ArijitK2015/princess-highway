<?php

/**
 * Class FactoryX_Instagram_Block_Adminhtml_Product
 */
class FactoryX_Instagram_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface{
    /**
     *
     */
    public function __construct()
    {
        $this->setTemplate('factoryx/instagram/product.phtml');
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->toHtml();
    }
}