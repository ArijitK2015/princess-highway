<?php
if ((string)Mage::getConfig()->getModuleConfig('GT_Speed')->active == 'true'){
    /**
     * Class FactoryX_Queryfier_Block_Page_Html_Head_Abstract
     */
    class FactoryX_Queryfier_Block_Page_Html_Head_Abstract extends GT_Speed_Block_Page_Html_Head {}
} else {
    /**
     * Class FactoryX_Queryfier_Block_Page_Html_Head_Abstract
     */
    class FactoryX_Queryfier_Block_Page_Html_Head_Abstract extends Mage_Page_Block_Html_Head {}
}
