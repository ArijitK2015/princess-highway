<?php

/**
 * Class FactoryX_ShippedFrom_Model_Auspost_Shipping_Accounts
 */
class FactoryX_ShippedFrom_Model_Auspost_Shipping_Accounts
    extends FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
{

    /**
     * @param $data
     * @return mixed
     */
    public function retrieveAuspostAccountDetails($data)
    {
        $accountNo = $data['account_no'];
        $apiKey = $data['api_key'];
        $apiPassword = $data['api_pwd'];

        /** @var AusPost\ShipTrack $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);

        $accountDetails = array();

        try {
            $accountDetails = $shipTrack->GetAccounts(array('account_number'    =>  $accountNo));
        } catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
            /** @todo something with the message */
        }

        if (is_array($accountDetails) && $accountDetails) {
            $data['name'] = array_key_exists('name', $accountDetails)
                ? $accountDetails['name']
                : "";
            $data['valid_from'] = array_key_exists('valid_from', $accountDetails)
                ? $accountDetails['valid_from']
                : "";
            $data['valid_to'] = array_key_exists('valid_to', $accountDetails)
                ? $accountDetails['valid_to']
                : "";
            $data['expired'] = array_key_exists('expired', $accountDetails)
                ? $accountDetails['expired']
                : "";
            $data['details_lodgement_postcode'] = (
                    array_key_exists('details', $accountDetails)
                    && array_key_exists('lodgement_postcode', $accountDetails['details'])
                )
                ? $accountDetails['details']['lodgement_postcode']
                : "";
            $data['details_abn'] = (
                    array_key_exists('details', $accountDetails)
                    && array_key_exists('abn', $accountDetails['details'])
                )
                ? $accountDetails['details']['abn']
                : "";
            $data['details_acn'] = (
                    array_key_exists('details', $accountDetails)
                    && array_key_exists('acn', $accountDetails['details'])
                )
                ? $accountDetails['details']['acn']
                : "";
            $data['details_contact_number'] = (
                    array_key_exists('details', $accountDetails)
                    && array_key_exists('contact_number', $accountDetails['details'])
                )
                ? $accountDetails['details']['contact_number']
                : "";
            $data['details_email_address'] = (
                    array_key_exists('details', $accountDetails)
                    && array_key_exists('email_address', $accountDetails['details'])
                )
                ? $accountDetails['details']['email_address']
                : "";
            $data['merchant_location_id'] = array_key_exists('merchant_location_id', $accountDetails)
                ? $accountDetails['merchant_location_id']
                : "";
            $data['credit_blocked'] = array_key_exists('credit_blocked', $accountDetails)
                ? $accountDetails['credit_blocked']
                : "";
            $data['postage_products'] = array_key_exists('postage_products', $accountDetails)
                ? $accountDetails['postage_products']
                : "";
            return $data;
        }

        return $data;
    }
}