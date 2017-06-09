<?php

use Auspost\Common\Auspost;
use Auspost\Shipping\ShippingClient;

/**
 * Class FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
 */
class FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
{
    /**
     * @param $apiKey
     * @param $apiPassword
     * @param $accountNo
     * @param bool $sink
     * @return ShippingClient
     */
    protected function initShipTrack($apiKey, $apiPassword, $accountNo, $sink = false)
    {
        $config = array(
            'auth_key'          =>  $apiKey,
            'auth_pass'         =>  $apiPassword,
            'account_no'        =>  $accountNo
        );

        if ($this->hlpAuspost()->isDevMode()) {
            $config['developer_mode'] = true;
        }

        if ($sink) {
            // specify where the body of a response will be saved
            $config['sink'] = $sink;
        }

        return Auspost::factory($config)->get('shipping');
    }

    /**
     * @param $locationId
     * @return bool|mixed
     */
    protected function getAccountNumberFromShipment($locationId)
    {
        $collection = Mage::getResourceModel('shippedfrom/account_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->addFieldToSelect('account_no')
            ->setPageSize(1)
            ->setCurPage(1);

        Mage::helper('shippedfrom')->log(sprintf("%s->collection->SQL=%s", __METHOD__, $collection->getSelect()->__toString()) );
        Mage::helper('shippedfrom')->log(sprintf("%s->collection->size=%d", __METHOD__, $collection->getSize()) );
        
        if ($locationId && $collection->getSize()) {
            $account = $collection->getFirstItem();
            Mage::helper('shippedfrom')->log(sprintf("%s->locationId=%s", __METHOD__, $locationId) );
            return $account->getAccountNo();
        }

        $storeCollection = Mage::getResourceModel('ustorelocator/location_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->addFieldToSelect('region')
            ->setPageSize(1)
            ->setCurPage(1);

        if ($locationId && $storeCollection->getSize()) {
            $location = $storeCollection->getFirstItem();
            $state = $location->getRegion();

            $accountCollection = Mage::getResourceModel('shippedfrom/account_collection')
                ->addFieldToSelect('account_no')
                ->addFieldToFilter('state', $state)
                ->setPageSize(1)
                ->setCurPage(1);

            if ($state && $accountCollection->getSize()) {
                $account = $accountCollection->getFirstItem();
                Mage::helper('shippedfrom')->log(sprintf("%s->state=%s", __METHOD__, $state) );
                return $account->getAccountNo();
            }
        }

        Mage::helper('shippedfrom')->log(sprintf("%s->global!", __METHOD__) );
        return $this->hlpAuspost()->getGlobalAccountNo();
    }

    /**
     * @param $locationId
     * @return bool|mixed
     */
    protected function getApiKeyFromShipment($locationId)
    {
        $collection = Mage::getResourceModel('shippedfrom/account_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->addFieldToSelect('api_key')
            ->setPageSize(1)
            ->setCurPage(1);

        if ($locationId && $collection->getSize()) {
            $account = $collection->getFirstItem();
            return $account->getApiKey();
        }

        $storeCollection = Mage::getResourceModel('ustorelocator/location_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->setPageSize(1)
            ->setCurPage(1);

        if ($locationId && $storeCollection->getSize()) {
            $location = $storeCollection->getFirstItem();
            $state = $location->getRegion();

            $accountCollection = Mage::getResourceModel('shippedfrom/account_collection')
                ->addFieldToSelect('api_key')
                ->addFieldToFilter('state', $state)
                ->setPageSize(1)
                ->setCurPage(1);

            if ($state && $accountCollection->getSize()) {
                $account = $accountCollection->getFirstItem();
                return $account->getApiKey();
            }
        }

        return $this->hlpAuspost()->getGlobalApiKey();
    }

    /**
     * @param $locationId
     * @return bool|mixed
     */
    protected function getApiPasswordFromShipment($locationId)
    {
        $collection = Mage::getResourceModel('shippedfrom/account_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->addFieldToSelect('api_pwd')
            ->setPageSize(1)
            ->setCurPage(1);

        if ($locationId && $collection->getSize()) {
            $account = $collection->getFirstItem();
            return $account->getApiPwd();
        }

        $storeCollection = Mage::getResourceModel('ustorelocator/location_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->setPageSize(1)
            ->setCurPage(1);

        if ($locationId && $storeCollection->getSize()) {
            $location = $storeCollection->getFirstItem();
            $state = $location->getRegion();

            $accountCollection = Mage::getResourceModel('shippedfrom/account_collection')
                ->addFieldToSelect('api_pwd')
                ->addFieldToFilter('state', $state)
                ->setPageSize(1)
                ->setCurPage(1);

            if ($state && $accountCollection->getSize()) {
                $account = $accountCollection->getFirstItem();
                return $account->getApiPwd();
            }
        }

        return $this->hlpAuspost()->getGlobalApiPwd();
    }

    /**
     * @param $locationId
     * @return bool|mixed
     */
    protected function getMerchantLocationIdFromShipment($locationId)
    {
        $collection = Mage::getResourceModel('shippedfrom/account_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->addFieldToSelect('merchant_location_id')
            ->setPageSize(1)
            ->setCurPage(1);

        if ($locationId && $collection->getSize()) {
            $account = $collection->getFirstItem();
            return $account->getMerchantLocationId();
        }

        $storeCollection = Mage::getResourceModel('ustorelocator/location_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->setPageSize(1)
            ->setCurPage(1);

        if ($locationId && $storeCollection->getSize()) {
            $location = $storeCollection->getFirstItem();
            $state = $location->getRegion();

            $accountCollection = Mage::getResourceModel('shippedfrom/account_collection')
                ->addFieldToSelect('merchant_location_id')
                ->addFieldToFilter('state', $state)
                ->setPageSize(1)
                ->setCurPage(1);

            if ($state && $accountCollection->getSize()) {
                $account = $accountCollection->getFirstItem();
                return $account->getMerchantLocationId();
            }
        }

        return "MASTER ACCOUNT MLID";
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param $creationSummary
     */
    protected function handleErrors(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry, $creationSummary)
    {
        $errors = explode("\n", $creationSummary['errors']['message']);
        list($errors, $url) = $this->getCleanUrl($errors);
        $message = $this->getCleanErrorMessage($errors);
        $queueEntry->setData('ap_last_message', $message)
            ->setData('ap_last_url', $url)
            ->save();
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $queueCollection
     * @param $creationSummary
     */
    protected function massHandleErrors(FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $queueCollection, $creationSummary)
    {
        $errors = explode("\n", $creationSummary['errors']['message']);
        list($errors, $url) = $this->getCleanUrl($errors);
        $message = $this->getCleanErrorMessage($errors);
        $queueCollection->massUpdate(array('ap_last_message' =>  $message, 'ap_last_url'    =>  $url));
    }

    /**
     * @return FactoryX_ShippedFrom_Helper_Data
     */
    protected function hlp()
    {
        return Mage::helper('shippedfrom');
    }

    /**
     * @return FactoryX_ShippedFrom_Helper_Auspost
     */
    protected function hlpAuspost()
    {
        return Mage::helper('shippedfrom/auspost');
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param array $data
     */
    protected function saveJsonRequest(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry, $data)
    {
        $queueEntry->setData('json_request', Mage::helper('core')->jsonEncode($data))
            ->save();
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $queueCollection
     * @param array $data
     */
    protected function massSaveJsonRequest(FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $queueCollection, $data)
    {
        $queueCollection->massUpdate(array('json_request' =>  Mage::helper('core')->jsonEncode($data)));
    }

    /**
     * @param $errors
     * @return array
     */
    protected function getCleanUrl($errors)
    {
        $url = array_pop($errors);
        $url = str_replace('[url]', '', $url);
        return array($errors, $url);
    }

    /**
     * @param $errors
     * @return mixed|string
     */
    protected function getCleanErrorMessage($errors)
    {
        $message = implode(' - ', $errors);
        $message = str_replace(array('[status code]', '[reason phrase]'), '', $message);
        return $message;
    }

    /**
     * @param $cronLogger
     * @param $message
     */
    protected function addCronLoggerMessage($cronLogger, $message)
    {
        if ($cronLogger !== null) {
            $cronLogger->addMessage($message);
        }
    }


}