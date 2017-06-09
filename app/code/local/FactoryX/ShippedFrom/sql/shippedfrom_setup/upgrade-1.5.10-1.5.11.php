<?php
/**
add accounts to fx_shippedfrom_account table

+----------------------------+------------------+------+-----+---------------------+----------------+
| Field                      | Type             | Null | Key | Default             | Extra          |
+----------------------------+------------------+------+-----+---------------------+----------------+
| api_key                    | varchar(255)     | NO   |     | NULL                |                |
| api_pwd                    | varchar(255)     | NO   |     | NULL                |                |
| account_no                 | varchar(255)     | NO   |     | NULL                |                |
+----------------------------+------------------+------+-----+---------------------+----------------+
*/

$installer = $this;
$installer->startSetup();

$accounts = array(
    // Dangerfield
    array(
        "Account-Number" => "0007352424",
        "Account-Key" => "40815062-8bd4-422e-a0aa-d9868490cb5e",
        "Account-Password" => "xd83b2a6e183a563d7cd"
    ),
    // Gorman
    array(
        "Account-Number" => "0007352521",
        "Account-Key" => "a8dbc4eb-359a-4faa-a7f5-e5ef8b9c4488",
        "Account-Password" => "xe49b0870ff1bb7c76ff"
    ),       
    // Alannah Hill 
    array(
        "Account-Number" => "0007352547",
        "Account-Key" => "9a2935dc-97d6-4e1d-b704-b31ce605444d",
        "Account-Password" => "x25275e877dcd9922d13"
    ),
    // Jack London
    array(
        "Account-Number" => "0007352563",
        "Account-Key" => "b1588135-e394-41a3-a0f9-a792f0bc1717",
        "Account-Password" => "x2b1b6729339cc336c6e"
    ),
    // Claude Maus
    array(
        "Account-Number" => "0007352571",
        "Account-Key" => "edaca45a-356f-4c1d-a3e4-4a5e2c2f11c9",
        "Account-Password" => "x80e73cf6a9cd7865005"
    ),
    // Factory X Print Room        
    array(
        "Account-Number" => "0007419696",
        "Account-Key" => "90afa460-d458-4da7-87be-7b559b57da3b",
        "Account-Password" => "xa46d8907ef1ed77a9f7"
    ),
    // Factory X
    array(
        "Account-Number" => "0007896991",
        "Account-Key" => "f1770b27-aa82-4467-a71c-3d73b05cd150",
        "Account-Password" => "x34cb551024e6bfce37e"
    )
);
// fx_shippedfrom_account
$tableName = $installer->getTable('shippedfrom/account');

foreach($accounts as $account) {
    $data = array(
        'account_no' => $account['Account-Number'],
        'api_key'    => $account['Account-Key'],
        'api_pwd'    => $account['Account-Password']
    );
    Mage::log(sprintf("%s->var=%s", __METHOD__, print_r($data, true)) );
    $model = Mage::getModel('shippedfrom/account')->setData($data); 
    $model->save();
}