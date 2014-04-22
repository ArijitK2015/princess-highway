<?php

class FactoryX_Lookbook_Model_Varien_Data_Form_Element_CategorySelect extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('combobox');
        $this->_prepareOptions();
    }

    public function getElementHtml()
    {
        $this->addClass('select');
        $html = '<select id="'.$this->getHtmlId().'" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).'>'."\n";

		$categories = Mage::helper('lookbook')->getCategoriesArray();
						
		foreach ($categories as $category) {
			$html.= $this->_optionToHtml(array(
				'value' => $category['value'],
				'label' => $category['label'],
				'level' => $category['level'])
			);
		}

        $html.= '</select>'."\n";
        $html.= $this->getAfterElementHtml();
		
        return $html;
    }

    protected function _optionToHtml($option)
    {
		$html = '<option value="'.$this->_escape($option['value']).'"';
		$html.= isset($option['level']) ? 'style="padding-left:'.($option['level']*5).'px"' : '';

		$html.= '>'.$this->_escape($option['label']). ' (' . $this->_escape($option['value']) . ')</option>'."\n";
		
        return $html;
    }

    protected function _prepareOptions()
    {
        $values = $this->getValues();
        if (empty($values)) {
            $options = $this->getOptions();
            if (is_array($options)) {
                $values = array();
                foreach ($options as  $value => $label) {
                    $values[] = array('value' => $value, 'label' => $label);
                }
            } elseif (is_string($options)) {
                $values = array( array('value' => $options, 'label' => $options) );
            }
            $this->setValues($values);
        }
    }

    public function getHtmlAttributes()
    {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'readonly', 'tabindex');
    }
}