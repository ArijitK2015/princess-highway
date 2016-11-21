<?php
/**
 * Adminhtml menuimage templates grid block action item renderer
 *
 * @category   FactoryX
 * @package    FactoryX_MenuImage
 * @author     Factory X Team <developers@factoryx.com.au>
 */

class FactoryX_MenuImage_Block_Adminhtml_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     *    Renderer for the action column
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        // Mage::helper('menuimage')->log(sprintf("%s->row->getId()=%s", __METHOD__, $row->getId()) );

        // Edit action
        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("*/*/edit", array('id' => $row->getId()))
            ),
            '#'	=> Mage::helper('menuimage')->__('Edit')
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
        // Mage::helper('menuimage')->log(sprintf("%s->actions=%s", __METHOD__, print_r($actions, true)) );
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }

}
