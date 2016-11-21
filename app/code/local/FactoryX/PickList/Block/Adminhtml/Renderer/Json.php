<?php
/**

*/

class FactoryX_PickList_Block_Adminhtml_Renderer_Json extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());

        // not supported php < 5.4
        //$data = json_decode($value, true);
        //$value = json_encode($data, JSON_PRETTY_PRINT);
        
        $value = Mage::helper('picklist')->pretty_json($value);
        return $value;
    }
}