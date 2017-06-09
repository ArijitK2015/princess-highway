<?php

$installer = $this;
$installer->startSetup();

/** @var FactoryX_StoreLocator_Model_Mysql4_Location_Collection $storeLocations */
$storeLocations = Mage::getResourceModel('ustorelocator/location_collection');

foreach ($storeLocations as $storeLocation) {

    $addressDisplay = $storeLocation->getAddressDisplay();
    if (!$addressDisplay) {
        continue;
    }

    $adressDisplay = str_replace(',', ' ', $addressDisplay);
    $storeLocation->setAddressDisplay($addressDisplay)->save();
}

$installer->endSetup();