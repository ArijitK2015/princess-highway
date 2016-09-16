<?php

/**
 * Class FactoryX_Homepage_Model_Varien_Data_Form_Element_DropdownImages
 */
class FactoryX_Homepage_Model_Varien_Data_Form_Element_DropdownImages extends Varien_Data_Form_Element_Abstract
{
    /**
     *    Constructor for the custom Dropdown Images element
     * @param array $attributes
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);
        // Radio buttons type
        $this->setType('select');
        $this->setExtType('combobox');
        $this->_prepareOptions();
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('select');
        $html = '<select id="'.$this->getHtmlId().'" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).'>'."\n";

        $value = $this->getValue();
        if (!is_array($value)) {
            $value = array($value);
        }

        if ($values = $this->getValues()) {
            foreach ($values as $key => $option) {
                if (!is_array($option)) {
                    $html.= $this->_optionToHtml(array(
                        'value' => $key,
                        'label' => $option),
                        $value
                    );
                }
                elseif (is_array($option['value'])) {
                    $html.='<optgroup label="'.$option['label'].'">'."\n";
                    foreach ($option['value'] as $groupItem) {
                        $html.= $this->_optionToHtml($groupItem, $value);
                    }
                    $html.='</optgroup>'."\n";
                }
                else {
                    $html.= $this->_optionToHtml($option, $value);
                }
            }
        }

        $html.= '</select>'."\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    /**
     * @param $option
     * @param $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected)
    {
        if (is_array($option['value'])) {
            $html ='<optgroup label="'.$option['label'].'">'."\n";
            foreach ($option['value'] as $groupItem) {
                $html .= $this->_optionToHtml($groupItem, $selected);
            }
            $html .='</optgroup>'."\n";
        }
        else {
            $html = '<option value="'.$this->_escape($option['value']).'"';
            $html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
            $html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
            if (in_array($option['value'], $selected)) {
                $html.= ' selected="selected"';
            }
            if (array_key_exists('image',$option))
            {
                $imgSrc = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/factoryx/homepage/".$this->_escape($option['image']);
                $html.= 'data-imagesrc="'.$imgSrc. '"/></option>'."\n";
            }
            else
            {
                $html.= '>'.$this->_escape($option['label']). '</option>'."\n";
            }
        }
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
}