<?php
mail('alvin@factoryx.com.au','fx install','message');


$installer = $this;
$installer->startSetup();

//Import CMS Block
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'cms_block.sql';
mail('alvin@factoryx.com.au','fx install',$path);
if (file_exists($path)) {
	$sql = file_get_contents($path);
	mail('alvin@factoryx.com.au','fx install',$sql);
	$installer->run($sql);
}

// Import Email Template


// Import CMS Page


$installer->endSetup();

?>