<?php
/**
loads in cms block & email templates
*/

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('fxinit');

// check if core has deployed its cms
$conn = Mage::getSingleton('core/resource')->getConnection('core_read');
$sql = "select data_version from core_resource where code = 'cms_setup';";
//$results = $conn->fetchAll($sql);

$dataVersion = $conn->fetchOne($sql);
if (!preg_match("/1\.6\.0\.0\.2/i", $dataVersion)) {
    Mage::log(sprintf("wrong cms_setup data_version '%s'!", $dataVersion) );
    return;
}

$sqlFiles = array(
    // CMS Block
    'cms_block.sql',
    // CMS Block Store
    'cms_block_store.sql',
    // CMS Page
    'cms_page.sql',
    // CMS Page Store
    'cms_page_store.sql',
    // Email Template
    'core_email_template.sql'
);

foreach($sqlFiles as $sqlFile) {
    $filePath = sprintf("%s/app/code/local/FactoryX/Init/sql/mysqldump/%s", Mage::getBaseDir(), $sqlFile);
    if (file_exists($filePath)) {
        Mage::log(sprintf("load sql '%s'...", $filePath) );
    	$sql = file_get_contents($filePath);
    	$installer->run($sql);
    	Mage::log(sprintf("import complete") );
    }
    else {
        Mage::log(sprintf("cannot find file '%s'", $filePath) );
    }
}

try {
    $helper->createPronav($this);
}
catch(Exception $ex) {
    // ignore
}

$installer->endSetup();

?>