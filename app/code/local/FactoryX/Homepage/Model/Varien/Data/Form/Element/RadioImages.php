<?php

class FactoryX_Homepage_Model_Varien_Data_Form_Element_RadioImages extends Varien_Data_Form_Element_Abstract 
{
	/**
	 *	Constructor for the custom Radio Images element
	 */
    public function __construct($data) 
	{
        parent::__construct($data);
		// Radio buttons type
        $this->setType('radios');
    }
 
    public function getSeparator()
    {
        $separator = $this->getData('separator');
        if (is_null($separator)) {
            $separator = '&nbsp;';
        }
        return $separator;
    }

    public function getElementHtml()
    {
        $html = '';
        $value = $this->getValue();
        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                $html.= $this->_optionToHtml($option, $value);
            }
        }
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    protected function _optionToHtml($option, $selected)
    {
		// Display images as radio buttons and hide the real buttons
		$skinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml'.DS.'default'.DS.'default';
        $html = '<div class="radioimages"><input type="radio"'.$this->serialize(array('name', 'class', 'style'));
        if (is_array($option)) {
            $html.= 'value="'.$this->_escape($option['value']).'"  id="'.$this->getHtmlId().$option['value'].'"';
            if ($option['value'] == $selected) {
                $html.= ' checked="checked"';
            }
            $html.= ' />';
            $html.= '<label class="inline" for="'.$this->getHtmlId().$option['value'].'"><img id="image-'.$this->getHtmlId().$option['value'].'" src="'.$skinUrl.DS.'images/factoryx/homepage/'.$option['image'].'" alt="'.$option['value'].'" /></label>';
        }
        elseif ($option instanceof Varien_Object) {
        	$html.= 'id="'.$this->getHtmlId().$option->getValue().'"'.$option->serialize(array('label', 'title', 'value', 'class', 'style'));
        	if (in_array($option->getValue(), $selected)) {
        	    $html.= ' checked="checked"';
        	}
        	$html.= ' />';
        	$html.= '<label class="inline" for="'.$this->getHtmlId().$option->getValue().'"><img id="image-'.$this->getHtmlId().$option['value'].'" src="'.$skinUrl.DS.'images/factoryx/homepage/'.$option->getImage().'" alt="'.$option->getValue().'" /></label>';
        }
        $html.= $this->getSeparator() . "</div>\n";
        return $html;
    }
}