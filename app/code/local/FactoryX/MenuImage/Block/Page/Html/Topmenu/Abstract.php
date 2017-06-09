<?php
if ((string)Mage::getConfig()->getModuleConfig('FactoryX_Framework')->active == 'true'){
    /**
     * Class FactoryX_MenuImage_Block_Page_Html_Topmenu_Abstract
     */
    class FactoryX_MenuImage_Block_Page_Html_Topmenu_Abstract extends FactoryX_Framework_Block_Page_Html_Topmenu {}
} else {
    /**
     * Class FactoryX_Queryfier_Block_Page_Html_Head_Abstract
     */
    class FactoryX_MenuImage_Block_Page_Html_Topmenu_Abstract extends Mage_Page_Block_Html_Topmenu {}
}
