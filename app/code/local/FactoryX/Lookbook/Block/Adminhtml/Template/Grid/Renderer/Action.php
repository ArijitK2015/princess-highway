<?php
/**
 * Adminhtml lookbook templates grid block action item renderer
 *
 * @category   FactoryX
 * @package    FactoryX_Lookbook
 * @author     Factory X Team <developers@factoryx.com.au>
 */

class FactoryX_Lookbook_Block_Adminhtml_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {
        
    public function render(Varien_Object $row)
    {
        //Mage::helper('lookbook')->log(sprintf("%s->row->getId()=%s", __METHOD__, $row->getId()) );

        // for href actions
        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("*/*/edit", array('id' => $row->getId()))
            ),
            '#'	=> Mage::helper('lookbook')->__('Edit')
        );

		// link to the frontend view
        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("lookbook/index/view", array('id' => $row->getId(),'_store' => 'default')),
                'target'=>	'_blank'
            ),
            '#'	=> Mage::helper('lookbook')->__('View')
        );
        return $this->_actionsToHtml($actions);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        //Mage::helper('lookbook')->log(sprintf("%s->actions=%s", __METHOD__, print_r($actions, true)) );
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }

}
