<?php
/**
	Because nothing is better than using our own store location module :)
**/

// Is the module active?
if (!Mage::getConfig()->getModuleConfig('Unirgy_StoreLocator')->is('active', 'true')) {
    Mage::log("the Unirgy_StoreLocator is not active, aborting configuration");
	return;
}

$installer = $this;
$installer->startSetup();	

$envConfig = array(
    "default" => array(
        "ustorelocator/general/google_geo_url"	=> 'http://maps.google.com/maps/geo',
        "ustorelocator/general/google_api_key" 	=> 'AIzaSyBi5ryKoyRWjHAc8KQM44pYtUczKJ4EQ5g',
        "ustorelocator/general/show_search" 	=> '1',
        "ustorelocator/general/show_map" 		=> '0',
        "ustorelocator/general/distance_units" 	=> 'mi'
    ),
    "prod" => array(
    ),
    "staging" => array(	       
    ),
    "dev" => array(
    )
);

$coreConfig = new Mage_Core_Model_Config();

// load default
foreach($envConfig["default"] as $path => $val) {
    Mage::log(sprintf("%s->%s: %s", __METHOD__, $path, $val) );
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

// load env
foreach($envConfig[FactoryX_Init_Helper_Data::_getEnv()] as $path => $val) {
    Mage::log(sprintf("%s->%s: %s", __METHOD__, $path, $val) );
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

// Create URL rewrite
$urlRewrite = Mage::getModel('core/url_rewrite')->loadByRequestPath('store-locator.html');
if (!$urlRewrite){
	Mage::getModel('core/url_rewrite')
		->setIsSystem(0)
		->setStoreId(1)
		->setIdPath('ustorelocator/location/map-custom')
		->setTargetPath('ustorelocator/location/map')
		->setRequestPath('store-locator.html')
		->save();
}

// Import stores
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'ustorelocator_location.sql';
if (file_exists($path)) $installer->run(file_get_contents($path));

// Delete gmap tables
$installer->run('
		DROP TABLE IF EXISTS gmapstrlocator_store;
		DROP TABLE IF EXISTS gmapstrlocator_products;
		DROP TABLE IF EXISTS gmapstrlocator;
	');

$installer->endSetup();
?>