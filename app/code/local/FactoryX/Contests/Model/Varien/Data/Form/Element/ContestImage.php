<?php

class FactoryX_Contests_Model_Varien_Data_Form_Element_ContestImage extends Varien_Data_Form_Element_Abstract 
{
    public function __construct($data) {
        parent::__construct($data);
        $this->setType('file');
    }
 
    public function getElementHtml() 
	{
		$html = '';
        if ($this->getValue()) {
            $url = $this->_getUrl();
            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media') . 'contest' . $url;
            }
            $html = '<a href="'.$url.'" onclick="imagePreview(\''.$this->getHtmlId().'_image\'); return false;"><img src="'.$url.'" id="'.$this->getHtmlId().'_image" title="'.$this->getValue().'" alt="'.$this->getValue().'" height="22" width="22" /></a> ';
        }
        $this->setClass('input-file');
        $html.= parent::getElementHtml();
        $html.= $this->_getDeleteCheckbox();
        return $html;
    }
 
    protected function _getDeleteCheckbox() {
        $html = '';
        if ($this->getValue()) {
            $label = Mage::helper('core')->__('Delete Image');
            $html .= '<span>';
            $html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
            $html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? '' : '').'> '.$label.'</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }
        return $html;
    }
 
    protected function _getHiddenInput() {
        return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
    }
 
    protected function _getUrl() {
        return $this->getValue();
    }
 
    public function getName() {
        return  $this->getData('name');
    }
}
	