<?php

/**
 * Class FactoryX_Framework_Block_Page_Html_Topmenu
 */
class FactoryX_Framework_Block_Page_Html_Topmenu extends Mage_Page_Block_Html_Topmenu
{
    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Varien_Data_Tree_Node $menuTree
     * @param string $childrenWrapClass
     * @return string
     */
    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass) {

        if (Mage::getSingleton('core/design_package')->getPackageName() == "factoryx") {
            $html = '';

            $children = $menuTree->getChildren();
            $parentLevel = $menuTree->getLevel();
            $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

            $counter = 1;
            $childrenCount = $children->count();

            $parentPositionClass = $menuTree->getPositionClass();
            $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

            foreach ($children as $child) {

                $child->setLevel($childLevel);
                $child->setIsFirst($counter == 1);
                $child->setIsLast($counter == $childrenCount);
                $child->setPositionClass($itemPositionClassPrefix . $counter);

                $outermostClassCode = '';
                $outermostClass = $menuTree->getOutermostClass();
                $attributes = '';

                if ($childLevel == 0 && $outermostClass) {
                    // Add dropdown bootstrap attributes if subcategories
                    if ($child->hasChildren()) {
                        $outermostClassCode = ' class="' . $outermostClass . ' dropdown-toggle" ';
                        $attributes = ' data-toggle="dropdown" role="button" aria-expanded="false"';
                    }
                    else {
                        $outermostClassCode = ' class="' . $outermostClass . '" ';
                    }
                    $child->setClass($outermostClass);
                }

                // Add dropdown bootstrap attributes if subcategories
                if ($child->hasChildren() && $outermostClassCode == "")
                {
                    $outermostClassCode = ' class="dropdown-toggle" ';
                    $attributes = ' data-toggle="dropdown" role="button" aria-expanded="false"';
                }

                // Add more dropdown attributes
                if ($child->hasChildren() && $childLevel > 0) {
                    $child->setClass($child->getClass() . ' dropdown-submenu');
                }
                $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
                $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . $attributes . '>'
                    . $this->escapeHtml($child->getName());
                if ($child->hasChildren())
                {
                    $html .= ' <span class="caret"></span>';
                }
                $html .= '</a>';

                if ($child->hasChildren()) {
                    if (!empty($childrenWrapClass)) {
                        $html .= '<div class="' . $childrenWrapClass . '">';
                    }
                    $html .= '<ul class="dropdown-menu level' . $childLevel . '" role="menu">';
                    if (Mage::getStoreConfigFlag('framework/navigation/show_view_all')) {
                        $html .= '<li class="level'. $childLevel .' view-all">';
                        $html .= '<a class="level'. $childLevel .'" href="'. $child->getUrl() .'">';
                        $html .= $this->__('View All') . ' ' . $this->escapeHtml($this->__($child->getName()));
                        $html .= '</a>';
                        $html .= '</li>';
                    }
                    $html .= $this->_getHtml($child, $childrenWrapClass);
                    $html .= '</ul>';

                    if (!empty($childrenWrapClass)) {
                        $html .= '</div>';
                    }
                }
                $html .= '</li>';
                $counter++;
            }
        }
        else {
            $html = parent::_getHtml($menuTree, $childrenWrapClass);
        }
        return $html;
    }
}
