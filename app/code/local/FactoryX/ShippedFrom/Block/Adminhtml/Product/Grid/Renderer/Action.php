<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Product_Grid_Renderer_Action
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Product_Grid_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renderer for the action column
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = array();
        
        // delete action
        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl("*/*/delete", array('id' => $row->getId()))
            ),
            '#'	=> Mage::helper('shippedfrom')->__('Delete')
        );
        // view action
        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl("*/*/view", array('id' => $row->getId())),
                'target' => '_blank'
            ),
            '#'	=> Mage::helper('shippedfrom')->__('View')
        );
        
        return $this->_actionsToHtml($actions);
    }

    /**
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions)
    {
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }

        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }

}
