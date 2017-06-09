<?php
/**
 * Adminhtml reports viewer templates grid block action item renderer
 *
 * @category   FactoryX
 * @package    FactoryX_ReportsViewer
 * @author     Factory X Team <developers@factoryx.com.au>
 */

class FactoryX_ReportsViewer_Block_Adminhtml_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        // for href actions
        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("*/*/edit", array('id' => $row->getData('report_id')))
            ),
            '#'	=> Mage::helper('reportsviewer')->__('View Details')
        );
                
        return $this->_actionsToHtml($actions);
    }

    /**
     * @param $value
     * @return string
     */
    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    /**
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions)
    {
        //Mage::helper('reportsviewer')->log(sprintf("%s->actions=%s", __METHOD__, print_r($actions, true)) );
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }

}
