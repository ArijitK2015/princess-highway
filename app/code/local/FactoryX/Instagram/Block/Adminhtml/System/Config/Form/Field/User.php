<?php
/**
 * iKantam LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the iKantam EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://magento.factoryx.com/store/license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * InstagramConnect Module to newer versions in the future.
 *
 * @category   Ikantam
 * @package    FactoryX_Instagram
 * @author     iKantam Team <support@factoryx.com>
 * @copyright  Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license    http://magento.factoryx.com/store/license-agreement  iKantam EULA
 */
class FactoryX_Instagram_Block_Adminhtml_System_Config_Form_Field_User
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected $_template = "factoryx/instagram/config/form/field/user.phtml";

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return mixed
     */
    public function getUserData()
    {
        $info = Mage::getModel('instagram/instagramauth')->getUserData();
        return $info;
    }

}
