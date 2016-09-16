<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Template_Edit_Renderer_Button
 */
class FactoryX_Homepage_Block_Adminhtml_Template_Edit_Renderer_Button extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     *    Renderer for the 'Change Layout' button
     * @param Varien_Data_Form_Element_Abstract $element
     * @throws Exception
     * @return string
     */
	public function render(Varien_Data_Form_Element_Abstract $element) 
	{
		$html = '<button type="button" class="scalable" onclick="window.location=\''.$this->getUrl('*/*/details', array('id' => $this->getRequest()->getParam('id'))).'\'"><span><span><span>'.$element->getLabel().'</span></span></span></button>';
		return $html;
	}
}