<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Action
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Action
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

        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl("*/*/delete", array('schedule_id' => $row->getScheduleId()))
            ),
            '#' => Mage::helper('shippedfrom')->__('Delete')
        );

        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl("*/*/view", array('schedule_id' => $row->getScheduleId())),
                'target' => '_blank'
            ),
            '#' => Mage::helper('shippedfrom')->__('View')
        );

        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl("*/*/print", array('schedule_id' => $row->getScheduleId()))
            ),
           '#' => Mage::helper('shippedfrom')->__('Print')
        );

        $actions[] = array(
            '@' => array(
                'href' => $this->getUrl("*/*/get", array('schedule_id' => $row->getScheduleId()))
            ),
           '#' => Mage::helper('shippedfrom')->__('Get')
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
