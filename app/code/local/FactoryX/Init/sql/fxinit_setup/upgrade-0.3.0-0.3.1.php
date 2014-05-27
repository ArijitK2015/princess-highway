<?php
/**
load OnePica_ImageCdn with aws s3 config
*/

$installer = $this;
$installer->startSetup();

if (!Mage::getConfig()->getModuleConfig('OnePica_ImageCdn')->is('active', 'true')) {
    Mage::log("the OnePica_ImageCdn is not active, aborting configuration");
    return;
}

$coreConfig = new Mage_Core_Model_Config();

/**
 * Save config value to DB
 * $config->saveConfig('design/head/demonotice', "1", 'default', 0);
 *
 * @param string $path
 * @param string $value
 * @param string $scope
 * @param int $scopeId
 * @return Mage_Core_Store_Config
 */

$envConfig = array(
    "default" => array(
        "imagecdn/general/status"           => NULL,
        "imagecdn/general/cache_method"     => 1,
        "imagecdn/general/cache_ttl"        => 5,
        "imagecdn/general/cache_check_size" => 1,
        "imagecdn/general/compression"      => 3,
        /*
        "imagecdn/ftp/host"
        "imagecdn/ftp/port"
        "imagecdn/ftp/user"
        "imagecdn/ftp/pass"
        "imagecdn/ftp/passive"
        "imagecdn/ftp/base"
        "imagecdn/ftp/url_base"
        "imagecdn/ftp/url_base_secure"
        "imagecdn/ftp/url_is_direct"
        "imagecdn/highwinds/username"
        "imagecdn/highwinds/password"
        "imagecdn/highwinds/apikey"
        "imagecdn/highwinds/base_dir"
        "imagecdn/highwinds/base_url"
        "imagecdn/rackspace/username"
        "imagecdn/rackspace/api_key"
        "imagecdn/rackspace/container"
        "imagecdn/rackspace/base_url"
        "imagecdn/coralcdn/url_base"
        "imagecdn/coralcdn/url_base_secure"
        */
    ),
    "prod" => array(
    ),
    "staging" => array(
        "imagecdn/general/status"               => "imagecdn/adapter_amazons3",
        "imagecdn/amazons3/access_key_id"       => "aws",
        "imagecdn/amazons3/secret_access_key"   => "pEth3CLDeAU9oWrTTMJ5jDMluk4UZrsfLhkO10jw",
        "imagecdn/amazons3/bucket"              => "ph-stage-media",
        "imagecdn/amazons3/url_base"            => "http://ph-stage-media.s3-website-ap-southeast-2.amazonaws.com",
        "imagecdn/amazons3/url_base_secure"     => "http://ph-stage-media.s3-website-ap-southeast-2.amazonaws.com"
    ),
    "dev" => array(
    )
);

// load env
foreach($envConfig[FactoryX_Init_Helper_Data::_getEnv()] as $path => $val) {
    Mage::log(sprintf("%s->%s: %s", __METHOD__, $path, $val) );
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

// update
Mage::getConfig()->reinit();
Mage::getConfig()->cleanCache();
Mage::app()->reinitStores();

$installer->endSetup();

?>