<?php
$installer = $this;
$installer->startSetup();

..Import CMS Block
$path = Mage::getBaseDir().'app/code/local/FactoryX/Init/sql/mysqldump/'.'cms_block_email.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
}

// Import Email Template


// Import CMS Page


$installer->endSetup();
