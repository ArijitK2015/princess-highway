<?php
/**
loads in cms block & email templates
*/

$email_content = "";

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('fxinit');

//Import CMS Block
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'cms_block.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
	$email_content .= "Script ran for CMS block import<br/>";
}else{
	$email_content .= "Cannot find CMS block dump<br/>";
}

//Import CMS Block Store
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'cms_block_store.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
	$email_content .= "Script ran for CMS block store import<br/>";
}else{
	$email_content .= "Cannot find CMS block store dump<br/>";
}

//Import CMS Page
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'cms_page.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
	$email_content .= "Script ran for CMS page import<br/>";
}else{
	$email_content .= "Cannot find CMS page dump<br/>";
}

//Import CMS Page Store
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'cms_page_store.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
	$email_content .= "Script ran for CMS page store import<br/>";
}else{
	$email_content .= "Cannot find CMS page store dump<br/>";
}

// Import Email Template
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'core_email_template.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);	
	$installer->run($sql);
	$email_content .= "Script ran for email template import<br/>";
}
else{
	$email_content .= "Cannot find email template dump<br/>";
}

try {
    $helper->createPronav($this);
}
catch(Exception $ex) {
    // ignore
}

//mail('alvin@factoryx.com.au','fx install',$email_content);

$installer->endSetup();

?>