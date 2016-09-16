<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Grid_Renderer_Status
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $statuses = Mage::getModel('homepage/status')->getOptionArray();
        $status = $statuses[$row->getStatus()];
        $html = $status;
        if ($status == "Automatic") {
            $html .= "<br/>Start: ";
            $html .= $row->getStartDate();
            $html .= "<br/>End: ";
            $html .= $row->getEndDate();
        }
        return $html;
    }
}
