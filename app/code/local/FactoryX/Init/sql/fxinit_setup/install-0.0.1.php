<?php
/**

## current scripts

install-0.0.1.php           loads in categories
upgrade-0.0.1-0.0.2.php     loads in attributes & attribute sets
upgrade-0.0.2-0.0.3.php     loads in cms block & email templates & cms page
upgrade-0.0.3-0.1.0.php     core config & magic zoom
upgrade-0.1.0-0.2.0.php 	loads in the store locations
upgrade-0.2.0-0.3.0.php     admin users
upgrade-0.3.0-0.3.1.php     module: OnePica_ImageCdn
upgrade-0.3.1-0.3.2.php     swatch images for base_colour used for colour filter
upgrade-0.3.2-0.3.3.php     module: FactoryX_Campaignmonitor
upgrade-0.3.3-0.3.4.php     module: Temando_Temando
upgrade-0.3.4-0.3.5.php     module: MagicToolbox_MagicZoom
upgrade-0.3.5-0.3.6.php     module: GT_Speed

...
add other scripts here and or rearrange scripts
...

## how to install what

# check version
select * from core_resource where code = 'fxinit_setup';

# update version
update core_resource set version = '0.0.1', data_version = '0.0.1' where code = 'fxinit_setup';
update core_resource set version = '0.3.5', data_version = '0.3.5' where code = 'fxinit_setup';

# reinstall everything
delete from core_resource where code = 'fxinit_setup';

# other storage if required
select * from core_config_data where path like '%fxinit%';
*/

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('fxinit');

// run reset_categories.sql
$path = sprintf("%s/app/code/local/FactoryX/Init/sql/mysqldump/reset_categories.sql", Mage::getBaseDir());
if (file_exists($path)) {
    Mage::log(sprintf("reset cats") );
	$sql = file_get_contents($path);
	$installer->run($sql);
}
else {
    Mage::log(sprintf("invalid path '%s', could not reset cats", $path) );
}


//Import categories
$path = sprintf("%s/app/code/local/FactoryX/Init/sql/resources/importCategory.txt", Mage::getBaseDir());
if (file_exists($path)) {
	$helper->createCategoryTree($path);
}
else {
    Mage::log(sprintf("invalid path '%s', could not init cats", $path) );
}

$installer->endSetup();

?>