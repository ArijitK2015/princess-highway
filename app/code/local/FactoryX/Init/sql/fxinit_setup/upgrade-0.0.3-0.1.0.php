<?php
/*
loads core config & magic zoom

known errors
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry

caused by mutiple inserts (see core_config_data_fix.sql)
*/

$installer = $this;
$installer->startSetup();

/*
// fix core_config_data (duplicates...)
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/core_config_data_fix.sql';
if (file_exists($path)) {
    $sql = file_get_contents($path);
    try {
        $installer->run($sql);
    }
    catch(Exception $ex) {
        Mage::log(sprintf("error loading '%s' : %s", $path, $ex->getMessage()) );
    }
}
else {
    Mage::log(sprintf("cannot find file '%s'", $path) );
}
*/

// import all module configuration from core_config_data
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/core_config_data.sql';
if (file_exists($path)) {
    $sql = file_get_contents($path);
    try {
        $installer->run($sql);
    }
    catch(Exception $ex) {
        Mage::log(sprintf("error loading '%s' : %s", $path, $ex->getMessage()) );
    }
}
else {
    Mage::log(sprintf("cannot find file '%s'", $path) );
}

$installer->endSetup();
?>