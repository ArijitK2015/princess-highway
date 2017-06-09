<?php

/**
 * Class FactoryX_Homepage_Model_Varien_Data_Form_Element_LayoutImage
 */
class FactoryX_Homepage_Model_Varien_Data_Form_Element_LayoutImage extends Varien_Data_Form_Element_Abstract
{
    /**
     *    Constructor for the custom Layout Image element
     * @param array $data
     */
    public function __construct($data) 
	{
        parent::__construct($data);
		// Note type
        $this->setType('note');
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
		// Just display the chosen layout in a span
		$skinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default';
        $html = '<span id="' . $this->getHtmlId() . '"><img src="' . $skinUrl.'/images/factoryx/homepage/'.$this->getText() . '.png" /></span>';
        $html.= $this->getAfterElementHtml();
        return $html;
    }
}