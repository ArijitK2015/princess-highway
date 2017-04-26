<?php

use Auspost\Shipping\Enum\AddressState;

/**
 * Class FactoryX_ShippedFrom_Model_Auspost_Shipping_Address
 */
class FactoryX_ShippedFrom_Model_Auspost_Shipping_Address extends FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
{
    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param $shipmentData
     * @throws Exception
     */
    public function validateShipmentAddressData(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry, $shipmentData)
    {
        if (!is_array($shipmentData)
            || !array_key_exists('from', $shipmentData)
            || !array_key_exists('to', $shipmentData)) {
            Mage::throwException(
                $this->hlp()->__(
                    "malformed shipment, please check the data: <pre>%s</pre>",
                    print_r($shipmentData, true)
                )
            );
        }

        $this->validateAddress($queueEntry, $shipmentData['from']);
        $this->validateAddress($queueEntry, $shipmentData['to']);
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param $shipmentData
     * @throws Exception
     */
    protected function validateAddress(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry, $shipmentData)
    {
        $accountNo = $this->getAccountNumberFromShipment($queueEntry->getShippedFrom());
        $apiKey = $this->getApiKeyFromShipment($queueEntry->getShippedFrom());
        $apiPassword = $this->getApiPasswordFromShipment($queueEntry->getShippedFrom());

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);

        // check all fields
        if (!array_key_exists('suburb', $shipmentData)
            || !array_key_exists('state', $shipmentData)
            || !array_key_exists('postcode', $shipmentData)
            || !array_key_exists('lines', $shipmentData)) {
            Mage::throwException(
                $this->hlp()->__(
                    "malformed address, please check the data: <pre>%s</pre>",
                    print_r($shipmentData, true)
                )
            );
        }

        if (empty($shipmentData['state'])) {
            Mage::throwException(
                $this->hlp()->__(
                    "address cannot have an empty state! check address: <pre>%s</pre>",
                    print_r($shipmentData, true)
                )
            );
        }

        $this->hlp()->log(sprintf("%s->state:%s", __METHOD__, $shipmentData['state']));
        $state = AddressState::normaliseState($shipmentData['state']);
        $suburb = trim($shipmentData['suburb']);
        $postcode = (int)$shipmentData['postcode'];
        $data = array(
            'suburb'    => $suburb,
            'state'     => $state,
            'postcode'  => $postcode
        );
        $this->hlp()->log(sprintf("%s->ValidateSuburb:%s", __METHOD__, print_r($data, true)));
        if (!$suburb
            || !$state
            || !$postcode) {
            Mage::throwException(
                $this->hlp()->__(
                    "Address has empty required fields: suburb='%s' state='%s' postcode='%s'",
                    $suburb,
                    $state,
                    $postcode
                )
            );
        }

        $result = $shipTrack->ValidateSuburb($data);
        $this->hlp()->log(sprintf("%s->result:%s", __METHOD__, print_r($result, true)));
        if (!$result['found']
            || (array_key_exists('results', $result)
                && !preg_grep(sprintf("/%s/i", $suburb), $result['results']))) {
            Mage::throwException(
                $this->hlp()->__(
                    "address 'suburb:%s|state:%s|postcode:%s' validation failed!",
                    $shipmentData['suburb'],
                    $state,
                    $shipmentData['postcode']
                )
            );
        }

        /*
        validate the shipping address: street number and street

        TODO: do via API?... im not sure how this gets done
        https://auspost.com.au/business-solutions/data-marketing-services/improve-your-data/address-data/address-validation
        */

        /*
        first check if the suburb, state or postcode is in the address as it shouldn't be
        */
        $address = implode(",", $shipmentData['lines']);
        if ($strictAddresses = false) {
            if ($this->hlp()->containsWord($address, $shipmentData['suburb']) !== false) {
                Mage::throwException(
                    $this->hlp()->__("suburb '%s' detected in address: %s", $shipmentData['suburb'], $address)
                );
            }
            if ($this->hlp()->containsWord($address, $state) !== false) {
                Mage::throwException($this->hlp()->__(sprintf("state '%s' detected in address: %s", $state, $address)));
            }
            // check if they are different
            if (strcasecmp($state, $shipmentData['state']) != 0) {
                if ($this->hlp()->containsWord($address, $shipmentData['state']) !== false) {
                    Mage::throwException(
                        $this->hlp()->__("state '%s' detected in address: %s", $shipmentData['state'], $address)
                    );
                }
            }
        }
        if ($this->hlp()->containsWord($address, $shipmentData['postcode']) !== false) {
            Mage::throwException(
                $this->hlp()->__("postcode '%s' detected in address: %s", $shipmentData['postcode'], $address)
            );
        }
        /*
        @TODO: additional check full state/region value e.g.
        select default_name from directory_country_region where code = 'WA' and country_id = 'AU';
        */
    }
}