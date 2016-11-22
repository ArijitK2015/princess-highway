<?php

/**
 * Class FactoryX_SortFx_Block_Adminhtml_System_Config_Tabs
 */
class FactoryX_SortFx_Block_Adminhtml_System_Config_Tabs extends Mage_Adminhtml_Block_System_Config_Tabs
{
    /**
     * @param $a
     * @param $b
     * @return int
     */
    protected function _sort($a, $b)
    {
        if ((string)$a->tab == "factoryx" || (string)$b->tab == "factoryx")
        {
            return strcmp((string)$a->label,(string)$b->label);
        }
        else return parent::_sort($a,$b);
    }
}