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
 * HouseConfigurator Module to newer versions in the future.
 *
 * @category   Ikantam
 * @package    Ikantam_HouseConfigurator
 * @author     iKantam Team <support@factoryx.com>
 * @copyright  Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license    http://magento.factoryx.com/store/license-agreement  iKantam EULA
 */
class FactoryX_Instagram_Model_Instagramauth
{
    const INSTAGRAM_SESSION_DATA_KEY = 'instagram_session_data';
    const INSTAGRAM_CONFIG_DATA_KEY = 'factoryx/instagram/instagram_data';

    /**
     * @return mixed
     */
    public function getUserData()
    {
        /** @var $session Mage_Core_Model_Session  */
        $session = Mage::getModel('core/session');
        $info = $session->getData('instagram_session_data');
        if (!$info) {
            $configDataKey = self::INSTAGRAM_CONFIG_DATA_KEY;
            $info = unserialize(Mage::getStoreConfig($configDataKey, 0));
        }
        return $info;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $configDataKey = self::INSTAGRAM_CONFIG_DATA_KEY;
        return (!!$this->getUserData() || Mage::getStoreConfig($configDataKey, 0));
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->getUserData()->access_token;
    }

}
