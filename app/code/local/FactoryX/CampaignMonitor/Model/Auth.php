<?php

/**
 * Class FactoryX_CampaignMonitor_Model_Auth
 */
class FactoryX_CampaignMonitor_Model_Auth
{
    /**
     * @return mixed
     */
    public function getUserData()
    {
        /** @var $session Mage_Core_Model_Session  */
        $session = Mage::getModel('core/session');
        // Get info from session
        $info = $session->getData(FactoryX_CampaignMonitor_Helper_Cm::CAMPAIGNMONITOR_SESSION_DATA_KEY);

        // If no info from session
        if (!$info)
        {
            // Get info from config
            $configDataKey = FactoryX_CampaignMonitor_Helper_Cm::CAMPAIGNMONITOR_CONFIG_DATA_KEY;
            $info = unserialize(Mage::getStoreConfig($configDataKey, 0));
        }

        return $info;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $configDataKey = FactoryX_CampaignMonitor_Helper_Cm::CAMPAIGNMONITOR_CONFIG_DATA_KEY;
        return (!!$this->getUserData() || Mage::getStoreConfig($configDataKey, 0));
    }

    /**
     * Get access token
     * @return mixed
     */
    public function getAccessToken()
    {
        return (isset($this->getUserData()->access_token) ? $this->getUserData()->access_token : false);
    }

    /**
     * Get refresh token
     * @return mixed
     */
    public function getRefreshToken()
    {
        return (isset($this->getUserData()->refresh_token) ? $this->getUserData()->refresh_token : false);
    }

}
