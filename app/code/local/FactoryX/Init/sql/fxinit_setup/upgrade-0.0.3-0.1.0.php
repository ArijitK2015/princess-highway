<?php
/*
loads core config & magic zoom

known errors
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry
design/head/demonotice
*/

$email_content = "";

$installer = $this;
$installer->startSetup();

// import all module configuration from core_config_data
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'core_config_data.sql';
if (file_exists($path)) {
    $sql = file_get_contents($path);
    try {
        $installer->run($sql);
    }
    catch(Exception $ex) {
        Mage::log(sprintf("%s->Error loading '%s' : %s", __METHOD__, $path, $ex->getMessage()) );
    }
    $email_content .= "Script ran for Core Config Data import<br/>";
}
else {
	$email_content .= "Cannot find Core Config Data dump<br/>";
}

$installer->endSetup();
?>