<?php

/**
 * Class FactoryX_SortFx_Block_Adminhtml_Page_Menu
 */
class FactoryX_SortFx_Block_Adminhtml_Page_Menu extends Mage_Adminhtml_Block_Page_Menu
{
    /**
     * Recursive Build Menu array
     *
     * @param Varien_Simplexml_Element $parent
     * @param string $path
     * @param int $level
     * @return array
     */
    protected function _buildMenuArray(Varien_Simplexml_Element $parent=null, $path='', $level=0)
    {
        if (is_null($parent)) {
            $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
        }

        $parentArr = array();
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {
            if (1 == $child->disabled) {
                continue;
            }

            $aclResource = 'admin/' . ($child->resource ? (string)$child->resource : $path . $childName);
            if (!$this->_checkAcl($aclResource) || !$this->_isEnabledModuleOutput($child)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = $this->_getHelperValue($child);

            $menuArr['sort_order'] = $child->sort_order ? (int)$child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string)$child->action, array('_cache_secret_key' => true));
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }

            $menuArr['active'] = ($this->getActive()==$path.$childName)
                || (strpos($this->getActive(), $path.$childName.'/')===0);

            $menuArr['level'] = $level;

            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path.$childName.'/', $level+1);
            }
            $parentArr[$childName] = $menuArr;

            $sortOrder++;
        }

        // Sort Factory X menu by name
        if ($path == "factoryx_menu/")
        {
            uasort($parentArr,array($this,'_strCmp'));
        }
        else uasort($parentArr, array($this, '_sortMenu'));

        while (list($key, $value) = each($parentArr)) {
            $last = $key;
        }
        if (isset($last)) {
            $parentArr[$last]['last'] = true;
        }

        return $parentArr;
    }

    /**
     * Sort menu comparison function for Factory X entries
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    protected function _strCmp($a, $b)
    {
        return strcmp($a['label'],$b['label']);
    }
}