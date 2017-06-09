<?php

/**
 * Class FactoryX_CustomGrids_Block_Adminhtml_Renderer_Address
 */
class FactoryX_CustomGrids_Block_Adminhtml_Renderer_Address extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row)
    {
        $address = $row->getData($this->getColumn()->getIndex());
        return preg_replace('/\n+/', ' ', $address);
    }
}