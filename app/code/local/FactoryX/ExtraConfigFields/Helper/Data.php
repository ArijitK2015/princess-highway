<?php

/**
 * Class FactoryX_ExtraConfigFields_Helper_Data
 */
class FactoryX_ExtraConfigFields_Helper_Data
    extends Mage_Core_Helper_Abstract {

    /**
     * Configuration path to override the store_email variable
     */
    const XML_PATH_SUPPORT_EMAIL_OVERRIDE = 'extraconfigfields/options/store_email_override';

    /**
     * @return mixed
     */
    public function getStoreEmail()
    {
        if ($identity = Mage::getStoreConfig(self::XML_PATH_SUPPORT_EMAIL_OVERRIDE)) {
            return Mage::getStoreConfig('trans_email/ident_' . $identity . '/email');
        } else {
            return false;
        }
    }

}
