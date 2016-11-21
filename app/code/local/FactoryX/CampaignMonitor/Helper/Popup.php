<?php
/**
 * Class FactoryX_CampaignMonitor_Helper_Popup
 */
class FactoryX_CampaignMonitor_Helper_Popup extends Mage_Core_Helper_Abstract
{
    /**
     * @return mixed
     */
    public function getDisplayText()
    {
        return Mage::getStoreConfig('newsletter/popup/displaytext');
    }

    /**
     * @return mixed
     */
    public function getTextToDisplay()
    {
        return Mage::getStoreConfig('newsletter/popup/texttodisplay');
    }

    /**
     * @return mixed
     */
    public function getReferers()
    {
        $referers = Mage::getStoreConfig('newsletter/popup/referers');
        if ($referers) return explode(',',$referers);
        else return false;
    }

    /**
     * @return array
     */
    public function getPagesOnly()
    {
        return explode("\r\n",Mage::getStoreConfig('newsletter/popup/pagesonly'));
    }

    /**
     * @return mixed
     */
    public function getTerms()
    {
        return Mage::getStoreConfig('newsletter/popup/terms');
    }

    /**
     * @return mixed
     */
    public function getBg()
    {
        return Mage::getStoreConfig('newsletter/popup/background');
    }

    /**
     * @return string
     */
    public function getBgCss()
    {
        return "/media/popup/".$this->getBg();
    }

    /**
     * @return string
     */
    public function getBgPath()
    {
        return Mage::getBaseDir('media')."/popup/".$this->getBg();
    }

    /**
     * @return mixed
     */
    public function getBgMobile()
    {
        return Mage::getStoreConfig('newsletter/popup/background_mobile');
    }

    /**
     * @return string
     */
    public function getBgMobileCss()
    {
        return "/media/popup/".$this->getBgMobile();
    }

    /**
     * @return string
     */
    public function getBgMobilePath()
    {
        return Mage::getBaseDir('media')."/popup/".$this->getBgMobile();
    }

    /**
     * @return mixed
     */
    public function getStyles()
    {
        return Mage::getStoreConfig('newsletter/popup/styles');
    }

    /**
     * @return mixed
     */
    public function getMobileStyles()
    {
        return Mage::getStoreConfig('newsletter/popup/mobilestyles');
    }

    /**
     * @return mixed
     */
    public function getThemeColor()
    {
        return Mage::getStoreConfig('newsletter/popup/themecolor');
    }

    /**
     * @return array|bool
     */
    public function getPreferredBrands()
    {
        $brands = Mage::getStoreConfig('newsletter/popup/preferred_brands');
        if ($brands) return preg_split('/\r\n|\r|\n/', $brands);
        else return false;
    }
}