<?php
/*
*/

$installer = $this;
$installer->startSetup();

/*
new reasons
*/
$reasons = array(
    array(
        'identifier'    => 'miscellaneous',
        'title'         => 'Miscellaneous',
        'sort_order'    => '13'
    )
);
foreach ($reasons as $reason) {
    $model = Mage::getModel('creditmemoreasons/reason');
    $model->load($reason['identifier'], 'identifier');

    if ($model->getTitle()) {
        Mage::helper('creditmemoreasons')->log(sprintf("reason '%s:%s' already exists", $reason['identifier'], $model->getTitle()));
    }
    else {
        Mage::helper('creditmemoreasons')->log(sprintf("add new reason '%s:%s'", $reason['identifier'], $reason['title']));
        $model->setData($reason)->save();
    }
}

$installer->endSetup();
