<?php

/**
 * Class FactoryX_ShippedFrom_Model_Auspost_Shipping_Track
 */
class FactoryX_ShippedFrom_Model_Auspost_Shipping_Track
    extends FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
{
    /**
     * @param $ids
     * @return string
     */
    public function track($ids)
    {
        $accountNo = $this->getAccountNumberFromShipment(
            $this->hlpAuspost()->getGlobalAccountNo()
        );
        $apiKey = $this->getApiKeyFromShipment(
            $this->hlpAuspost()->getGlobalApiKey()
        );
        $apiPassword = $this->getApiPasswordFromShipment(
            $this->hlpAuspost()->getGlobalApiPwd()
        );

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);

        try {
            $trackingInfo = $shipTrack->TrackItems(array('tracking_ids'    =>  $ids));
        } catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
        }

        if (!$trackingInfo && $message) {
            return "";
        } else if (array_key_exists('errors', $trackingInfo)) {
            return "";
        } else {
            return $trackingInfo;
        }
    }
}