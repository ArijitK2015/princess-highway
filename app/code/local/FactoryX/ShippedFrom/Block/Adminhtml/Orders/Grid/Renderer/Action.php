<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Orders_Grid_Renderer_Action
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Orders_Grid_Renderer_Action
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

        if ($row->getApOrderId()) {
            // Get Summary action
            $actions[] = array(
                '@' => array(
                    'href' => $this->getUrl("*/*/summary", array('order_id' => $row->getApOrderId())),
                    'target' => '_blank'
                ),
                '#' =>  Mage::helper('shippedfrom')->__('Get Summary')
            );

            // Pay action
            $actions[] = array(
                '@' => array(
                    'href'  => $this->getUrl("*/*/pay", array('order_id' => $row->getApOrderId()))
                ),
                '#' =>  Mage::helper('shippedfrom')->__('Create Payment')
            );

            // Update action
            $actions[] = array(
                '@' => array(
                    'href'  => $this->getUrl("*/*/update", array('order_id' => $row->getApOrderId()))
                ),
                '#' =>  Mage::helper('shippedfrom')->__('Update')
            );
        }

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
