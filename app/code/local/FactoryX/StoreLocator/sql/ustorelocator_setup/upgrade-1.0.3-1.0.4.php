<?php

$installer = $this;

$installer->startSetup();

// Set store that has lat long to is_featured = yes
$this->run("
     update ustorelocator_location set is_featured = 1 where ((latitude != 0 AND longitude != 0) OR (latitude != NULL AND longitude != NULL));
");

// Set store that has 0 or NULL lat longto is_featured = yes
$this->run("
    update ustorelocator_location set is_featured = 0 where ((latitude = 0 AND longitude = 0) OR (latitude = NULL AND longitude = NULL));
");

$installer->endSetup();