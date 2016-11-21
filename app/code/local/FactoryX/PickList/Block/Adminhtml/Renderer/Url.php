<?php
/**

*/

class FactoryX_PickList_Block_Adminhtml_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return mixed|string
     */
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $value = $this->convertPathToUrl($value);
        return $value;
    }

    /**
     * @param $path
     * @return string
     */
    public function convertPathToUrl($path) {
        //Mage::helper('picklist')->log(sprintf("%s->path=%s", __METHOD__, $path));        
        // match something
        if (preg_match('#[^\0]+#', $path)) {
            $fullPath = sprintf("%s/%s", Mage::getBaseDir(), $path);
            //Mage::helper('picklist')->log(sprintf("%s->fullPath=%s", __METHOD__, $fullPath));
            if (file_exists($fullPath)) {
                $url = sprintf("%s%s", Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), $path);
                $path = sprintf("<a target='_blank' rel='noopener noreferrer' href='%s'>%s</a>", $url, $path);
            }
            else {
                //$path = sprintf("DELETED:%s", $path);
                $path = sprintf("%s", $path);
            }
        }
        return $path;
    }
}