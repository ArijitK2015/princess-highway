<?php
/**
 * Who:  Alvin Nguyen
 * When: 26/09/2014
 * Why:  
 */

class FactoryX_ImageCdn_Block_Adminhtml_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action{

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('imagecdn');

        // for href actions
        $actions[] = array(
            'url' => array(
                'href'  => $this->getUrl("*/*/reupload", array('id' => $row->getId()))
            ),
            'caption'	=> $helper ->__('Upload')
        );

        // link to the frontend view
        $actions[] = array(
            'url' => array(
                'href'  => $this->getUrl("*/*/check", array('id' => $row->getId()))
            ),
            'caption'	=> $helper ->__('Check')
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
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['url']);
            $html[] = '<a target="_blank" rel="noopener noreferrer" ' . $attributesObject->serialize() . '>' . $action['caption'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }

}