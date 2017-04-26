<?php

$installer = $this;
$installer->startSetup();

/** @var FactoryX_StoreLocator_Model_Mysql4_Location_Collection $storeLocations */
$storeLocations = Mage::getResourceModel('ustorelocator/location_collection');

/** @var FactoryX_StoreLocator_Helper_Data $hlp */
$hlp = Mage::helper('ustorelocator');

/** @var FactoryX_StoreLocator_Model_Location $storeLocation */
/**
 * @param FactoryX_StoreLocator_Model_Location $storeLocation
 * @return bool
 */
function needsUpdate($storeLocation)
{
    return !$storeLocation->getSuburb()
        || !$storeLocation->getPostcode();
}

/**
 * @param $matches
 * @return mixed
 */
function getPostCode($matches)
{
    $matchesArray = $matches[count($matches) - 1];
    $postcode = $matchesArray[count($matchesArray) - 1];
    return $postcode;
}

/**
 * @param $conn
 * @param $postcode
 * @return mixed
 */
function fetchCityBasedOnPostcode($installer, $postcode)
{
    return $installer->getConnection()->fetchAll('SELECT city FROM ' . Mage::getSingleton('core/resource')->getTableName('australia_postcode') . ' WHERE postcode = ' . $postcode);
}

/**
 * @param $storeLocation
 * @param $postcode
 * @param $cityFound
 * @param $hlp
 * @param $addressDisplay
 */
function updateLocation($storeLocation, $postcode, $cityFound, $hlp, $addressDisplay)
{
    $storeLocation->setPostcode($postcode);
    $storeLocation->setSuburb($cityFound);
    $stripAddress = $hlp->strLreplace($postcode, '', $addressDisplay);
    $stripAddress = $hlp->strLreplace($cityFound, '', $stripAddress);
    $storeLocation->setAddressDisplay($stripAddress);
    $storeLocation->save();
}

foreach ($storeLocations as $storeLocation) {

    if (needsUpdate($storeLocation)) {

        $addressDisplay = $storeLocation->getAddressDisplay();
        if (!$addressDisplay) {
            continue;
        }

        if (preg_match_all('/\d{4}/', $addressDisplay, $matches)) {
            $postcode = getPostCode($matches);
            $results = fetchCityBasedOnPostcode($installer, $postcode);
            $cityFound = false;
            foreach ($results as $result) {
                foreach ($result as $key => $city) {
                    if (preg_match("/$city/", $addressDisplay)) {
                        $cityFound = $city;
                    } else {
                        continue;
                    }
                }
            }

            if ($cityFound) {
                updateLocation($storeLocation, $postcode, $cityFound, $hlp, $addressDisplay);
            } else {
                $hlp->log(sprintf("No matching city found while auto converting locator with id = %s", $storeLocation->getLocationId()));
            }
        } else {
            $hlp->log(sprintf("No matching postcode found while auto converting locator with id = %s", $storeLocation->getLocationId()));
        }
    }
}

$installer->endSetup();