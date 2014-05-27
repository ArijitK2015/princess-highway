<?php
/**
load GT_Speed
*/

$installer = $this;
$installer->startSetup();

if (!Mage::getConfig()->getModuleConfig('GT_Speed')->is('active', 'true')) {
    Mage::log("the GT_Speed is not active, aborting configuration");
    return;
}

$envConfig = array(
    "default" => array(
        'gtspeed/cssjs/min_css'         => '1',
        'gtspeed/cssjs/merge_css'       => '1',
        'gtspeed/cssjs/min_js'          => '1',
        'gtspeed/cssjs/merge_js'        => '1',
        'gtspeed/cssdebug/errorlogger'  => '0',
        'gtspeed/cssdebug/debugflag'    => '0',
        'gtspeed/imgopt/gifutil'        => 'gifsicle',
        'gtspeed/imgopt/gifutilopt'     => '-b -O3',
        'gtspeed/imgopt/jpgutil'        => 'jpegoptim',
        'gtspeed/imgopt/jpgutilopt'     => '--strip-all',
        'gtspeed/imgopt/pngutil'        => 'optipng',
        'gtspeed/imgopt/pngutilopt'     => '-o5',
        'gtspeed/imgpath/paths'         => 'media,skin,js',
        'gtspeed/imgdebug/imgoutput'    => '0',
        'gtspeed/cron/enabled'          => '0',
        'gtspeed/cron/frequency'        => 'D',
        'gtspeed/cron/time'                     => '00,00,00',
        'gtspeed/cron/error_email'              => NULL,
        'gtspeed/cron/error_email_identity'     => 'general',
        'gtspeed/cron/error_email_template'     => 'gtspeed_cron_error_email_template',
        'gtspeed/expires/enabled'               => '1',
        'gtspeed/expires/filetypes'             => 'css,js,jpg,png,gif',
        'gtspeed/expires/time'                  => '5259487',
        'gtspeed/unsecure/base_skin_url'        => '{{unsecure_base_url}}',
        'gtspeed/unsecure/base_js_url'          => '{{unsecure_base_url}}',
        'gtspeed/secure/base_skin_url'          => '{{secure_base_url}}',
        'gtspeed/secure/base_js_url'            => '{{secure_base_url}}',
        'crontab/jobs/gtspeed_optimize_images/schedule/cron_expr'   => '0 0 * * *',
        'crontab/jobs/gtspeed_optimize_images/run/model'            => 'gtspeed/observer::process'        
    ),
    "prod" => array(
    ),
    "staging" => array(
    ),
    "dev" => array(
        'gtspeed/cssjs/min_css'         => '0',
        'gtspeed/cssjs/merge_css'       => '0',
        'gtspeed/cssjs/min_js'          => '0',
        'gtspeed/cssjs/merge_js'        => '0',
        'gtspeed/expires/enabled'       => '0'
    )
);

$coreConfig = new Mage_Core_Model_Config();

// load default
foreach($envConfig["default"] as $path => $val) {
    //Mage::log(sprintf("%s: %s", $path, $val) );
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

// load env
Mage::log(sprintf("%s->load env=%s", __METHOD__, FactoryX_Init_Helper_Data::_getEnv()) );
foreach($envConfig[FactoryX_Init_Helper_Data::_getEnv()] as $path => $val) {
    //Mage::log(sprintf("%s: %s", $path, $val) );
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

// update
Mage::getConfig()->reinit();
Mage::getConfig()->cleanCache();
Mage::app()->reinitStores();

$installer->endSetup();

?>