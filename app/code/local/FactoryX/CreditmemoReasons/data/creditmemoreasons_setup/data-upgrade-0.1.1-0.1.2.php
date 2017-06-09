<?php
/*
*/
$reasons = array(
    array(
        'identifier' => 'failure_to_find',
        'title' => 'Failure To Find',
        'sort_order' => '1',
    ),
    array(
        'identifier' => 'return_arrived_too_late',
        'title' => 'Return - Arrived Too Late',
        'sort_order' => '2',
    ),    
    array(
        'identifier' => 'return_does_not_fit',
        'title' => 'Return - Does Not Fit',
        'sort_order' => '3',
    ),
    array(
        'identifier' => 'return_duplicate',
        'title' => 'Return - Duplicate Order',
        'sort_order' => '4',
    ),    
    array(
        'identifier' => 'return_exchange',
        'title' => 'Return - Exchange',
        'sort_order' => '5',
    ),
    array(
        'identifier' => 'return_faulty',
        'title' => 'Return - Faulty',
        'sort_order' => '6',
    ),
    array(
        'identifier' => 'return_incorrect_item',
        'title' => 'Return - Incorrect Item',
        'sort_order' => '7',
    ),
    array(
        'identifier' => 'return_not_as_described',
        'title' => 'Return - Not As Described',
        'sort_order' => '8',
    ),
    array(
        'identifier' => 'return_poor_quality',
        'title' => 'Return - Poor Quality',
        'sort_order' => '9',
    ),        
    array(
        'identifier' => 'suspected_fraud',
        'title' => 'Suspected Fraud',
        'sort_order' => '10',
    ),
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