<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Label
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Label
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renderer for the action column
     * @param Varien_Object $row
     * @return string
     */    
    public function render(Varien_Object $row)
    {
        $url = parent::render($row);
        $retVal = "";
        $data = $row->getData();
        if (array_key_exists('ap_consignment_id', $data)) {
            $linkText = sprintf("%s.pdf", $data['ap_consignment_id']);
        } else {
            $linkText = $url;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            $retVal = sprintf("<a href='%s' target='_blank' rel='noopener noreferrer'>%s</span>", $url, $linkText);
        }

        return $retVal;
    }
}