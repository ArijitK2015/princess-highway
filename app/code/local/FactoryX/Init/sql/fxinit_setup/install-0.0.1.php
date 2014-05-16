<?php

$email_content = "":

$installer = $this;
$installer->startSetup();

//Import CMS Block
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'cms_block.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	mail('alvin@factoryx.com.au','fx install',$sql);
	$installer->run($sql);
	$email_content .= "Script ran for CMS block import<br/>";
}else{
	$email_content .= "Cannot find CMS block dump<br/>";
}

// Import Email Template
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'core_email_template.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	mail('alvin@factoryx.com.au','fx install',$sql);
	$installer->run($sql);
	$email_content .= "Script ran for email template import<br/>";
}else{
	$email_content .= "Cannot find email template dump<br/>";
}

// Import CMS Page


mail('alvin@factoryx.com.au','fx install','message');

$installer->endSetup();

?>