<?php
/*
inverse sql statements for testing
update core_resource set data_version = '0.2.1' where code = 'creditmemoreasons_setup';
update sales_flat_creditmemo set reason = 'suspected_fraud' where reason = 'fraud_suspected'
update fx_creditmemoreasons_reasons set identifier = 'suspected_fraud' where identifier = 'fraud_suspected';
delete from fx_creditmemoreasons_reasons where identifier = 'fraud_charge_back';
delete from fx_creditmemoreasons_reasons where identifier = 'fraud_confirmed';
*/

$installer = $this;
$installer->startSetup();

/*
new reasons
*/
$reasons = array(
    array(
        'identifier'    => 'fraud_charge_back',
        'title'         => 'Fraud - Charge Back',
        'sort_order'    => '11'
    ),
    array(
        'identifier'    => 'fraud_confirmed',
        'title'         => 'Fraud - Confirmed Fraud',
        'sort_order'    => '12'
    )
);
foreach ($reasons as $reason) {
    $model = Mage::getModel('creditmemoreasons/reason');
    $model->load($reason['identifier'], 'identifier');

    if ($model->getTitle()) {
        Mage::helper('creditmemoreasons')->log(sprintf("reason '%s:%s' already exists", $reason['identifier'], $model->getTitle()));
    }
    else {
        Mage::helper('creditmemoreasons')->log(sprintf("add new reason '%s:%s' already exists", $reason['identifier'], $reason['title']));
        $model->setData($reason)->save();
    }
}

/*
update reasons
*/
$reasonUpdates = array(
    'suspected_fraud' => array(
        'identifier'    => 'fraud_suspected',
        'title'         => 'Fraud - Suspected Fraud',
        'sort_order'    => '10'
    )
);

foreach ($reasonUpdates as $oldIdentifier => $reason) {

    $model = Mage::getModel('creditmemoreasons/reason');
    $model->load($reason['identifier'], 'identifier');

    if ($model->getTitle()) {
        Mage::helper('creditmemoreasons')->log(sprintf("reason '%s:%s' already updated ...", $reason['identifier'], $model->getTitle()));
    }
    else {
        Mage::helper('creditmemoreasons')->log(sprintf("%s->update: %s", get_class($this), $oldIdentifier));
        $model = Mage::getModel('creditmemoreasons/reason');
        $model->load($oldIdentifier, 'identifier');
        $model->setData($reason)->save();
    
        // update sales_flat_creditmemo
        $sql = sprintf("update sales_flat_creditmemo set reason = '%s' where reason = '%s'", $reason['identifier'], $oldIdentifier);
        Mage::helper('creditmemoreasons')->log(sprintf("%s->sql: %s", get_class($this), $sql));
        $installer->run($sql);
        // update sales_flat_creditmemo_grid
        $sql = sprintf("update sales_flat_creditmemo_grid set reason = '%s' where reason = '%s'", $reason['identifier'], $oldIdentifier);
        Mage::helper('creditmemoreasons')->log(sprintf("%s->sql: %s", get_class($this), $sql));
        $installer->run($sql);
        Mage::helper('creditmemoreasons')->log(sprintf("%s->delete: %s", get_class($this), $oldIdentifier));
        $model->load($oldIdentifier, 'identifier')->delete();
    }
}

$installer->endSetup();
