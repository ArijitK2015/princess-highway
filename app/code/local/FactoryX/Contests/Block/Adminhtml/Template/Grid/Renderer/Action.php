<?php
/**
 * Adminhtml contests templates grid block action item renderer
 *
 * @category   FactoryX
 * @package    FactoryX_Contests
 * @author     Factory X Team <developers@factoryx.com.au>
 */

class FactoryX_Contests_Block_Adminhtml_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {
        
    public function render(Varien_Object $row)
    {
        //Mage::helper('contests')->log(sprintf("%s->row->getId()=%s", __METHOD__, $row->getId()) );

        // for href actions
        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("*/*/edit", array('id' => $row->getId()))
            ),
            '#'	=> Mage::helper('customer')->__('Edit')
        );

        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("*/*/view", array('id' => $row->getId())),
                'target'=>	'_blank'
            ),
            '#'	=> Mage::helper('customer')->__('View')
        );
        return $this->_actionsToHtml($actions);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        //Mage::helper('contests')->log(sprintf("%s->actions=%s", __METHOD__, print_r($actions, true)) );
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }

}
