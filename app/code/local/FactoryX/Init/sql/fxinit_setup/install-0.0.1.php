<?php
/**

## current scripts

install-0.0.1.php           loads in categories
upgrade-0.0.1-0.0.2.php     loads in attributes & attribute sets
upgrade-0.0.2-0.0.3.php     loads in cms block & email templates
...
add other scripts here and or rearrange scripts
...
upgrade-0.0.3-0.1.0.php     loads in config data


## how to install what

# check version
select * from core_resource where code = 'fxinit_setup';

# update version
update core_resource set version = '0.0.1', data_version = '0.0.1' where code = 'fxinit_setup';

# reinstall everything
delete from core_resource where code = 'fxinit_setup';

# other storage if required
select * from core_config_data where path like 'fxinit';
*/

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('fxinit');

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