<?php

/**
 * Class FactoryX_MenuImage_Block_Page_Html_Topmenu
 */
class FactoryX_MenuImage_Block_Page_Html_Topmenu extends FactoryX_MenuImage_Block_Page_Html_Topmenu_Abstract
{

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Varien_Data_Tree_Node $menuTree
     * @param string $childrenWrapClass
     * @return string
     */
    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        if (Mage::getSingleton('core/design_package')->getPackageName() == "factoryx")
        {
            $html = '';

            $children = $menuTree->getChildren();
            $parentLevel = $menuTree->getLevel();
            $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

            $counter = 1;
            $childrenCount = $children->count();

            $parentPositionClass = $menuTree->getPositionClass();
            $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

            foreach ($children as $child) {

                // Check menu
                $catId = str_replace('category-node-','',$child->getId());
                $menuImage = Mage::getModel('menuimage/menuimage')->getCollection()->addStatusFilter(1)->addCategoryFilter($catId);
                $menuImageBlocks = false;
                $imagesArray = array();
                if ($menuImage->getSize())
                {
                    $menuImageBlocks = Mage::getModel('menuimage/block')->getCollection()->addMenuimageFilter($menuImage->getFirstItem()->getId())->addAttributeToSort('sort_order');
                    $imagesArray = Mage::helper('menuimage')->generateBlocksArray($menuImageBlocks);
                }

                $child->setLevel($childLevel);
                $child->setIsFirst($counter == 1);
                $child->setIsLast($counter == $childrenCount);
                // Add full width class if menu images are found
                if (!empty($imagesArray))
                {
                    $child->setPositionClass($itemPositionClassPrefix . $counter . ' fx-full-width');
                }
                else
                {
                    $child->setPositionClass($itemPositionClassPrefix . $counter);
                }

                $outermostClassCode = '';
                $outermostClass = $menuTree->getOutermostClass();
                $attributes = '';

                if ($childLevel == 0 && $outermostClass) {
                    // Add dropdown bootstrap attributes if subcategories
                    if ($child->hasChildren())
                    {
                        $outermostClassCode = ' class="' . $outermostClass . ' dropdown-toggle" ';
                        $attributes = ' data-toggle="dropdown" role="button" aria-expanded="false"';
                    }
                    else
                    {
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

                    $html .= '<ul class="dropdown-menu level' . $childLevel;
                    if (!empty($imagesArray)) {
                        $html .= ' menu-image-ul';
                    }
                    $html .= '" role="menu">';
                    $html .=     '<li class="level'. $childLevel .' view-all">';
                    $html .=         '<a class="level'. $childLevel .'" href="'. $child->getUrl() .'">';
                    $html .=             $this->__('View All') . ' ' . $this->escapeHtml($this->__($child->getName()));
                    $html .=         '</a>';
                    $html .=     '</li>';
                    $html .= $this->_getHtml($child, $childrenWrapClass);
                    // Add the menu images
                    if (!empty($imagesArray))
                    {
                        $html .= '<div class="menu-image">';
                        foreach($imagesArray as $catMenuImage)
                        {
                            $html .= '<span><a href="'.$catMenuImage['url'].'"><img height="200" src="'.$catMenuImage['image'].'" alt="'.$this->escapeHtml($catMenuImage['alt']).'"/></a></span>';
                        }
                        $html .= '</div>';
                    }
                    $html .= '</ul>';

                    if (!empty($childrenWrapClass)) {
                        $html .= '</div>';
                    }
                }
                $html .= '</li>';

                $counter++;
            }

            return $html;
        }
        else return parent::_getHtml($menuTree, $childrenWrapClass);
    }
}
