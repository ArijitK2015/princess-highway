<?php

$appRoot = dirname(__FILE__) . "./../../../../../..";
if (defined('MAGENTO_ROOT')) {
    $appRoot = MAGENTO_ROOT;
}

include_once $appRoot . "/lib/createsend/csrest_clients.php";
include_once $appRoot . "/lib/createsend/csrest_subscribers.php";
include_once $appRoot . "/lib/createsend/csrest_lists.php";
include_once $appRoot . "/lib/createsend/csrest_general.php";
include_once $appRoot . "/lib/createsend/csrest_segments.php";

/**
 * Class FactoryX_CampaignMonitor_Helper_Cm
 * This helper contains every createsend calls, no need to include the calls directly in other files, use this helper to deal with CM
 */
class FactoryX_CampaignMonitor_Helper_Cm extends Mage_Core_Helper_Abstract
{
    const CAMPAIGNMONITOR_SESSION_DATA_KEY = 'campaignmonitor_session_data';
    const CAMPAIGNMONITOR_CONFIG_DATA_KEY = 'newsletter/campaignmonitor/campaignmonitor_data';

    /**
     * Campaign Monitor Authentication fields
     */
    protected $_auth;
    protected $_listId;
    protected $_apiKey;

    /**
     * Initiate the authentication
     * @param null $listId
     */
    protected function _initCampaignMonitor($listId = null)
    {
        // Get CM required info
        if (!isset($this->_auth))
        {
            $this->_auth = $this->_hlp()->getAuth();
        }
        // If a list is provide, replace the existing
        if (!is_null($listId))
        {
            $this->_listId = $listId;
        }
        elseif(!isset($this->_listId))
        {
            $this->_listId = $this->_hlp()->getListId();
        }
        if (!isset($this->_apiKey))
        {
            $this->_apiKey = $this->_hlp()->getApiKey();
        }
    }

    /**
     * Data helper
     * @return FactoryX_CampaignMonitor_Helper_Data
     */
    protected function _hlp()
    {
        return Mage::helper('campaignmonitor');
    }

    /**
     * Construct a client for subscribers
     * @return CS_REST_Subscribers
     */
    public function subscriberClient()
    {
        try
        {
            return new CS_REST_Subscribers($this->_listId, $this->_auth);
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error creating a subscriber client: ".$e->getMessage());
            return;
        }
    }

    /**
     * Construct a client for lists
     * @return CS_REST_Lists
     */
    public function listClient()
    {
        try
        {
            return new CS_REST_Lists($this->_listId, $this->_auth);
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error creating a list client: ".$e->getMessage());
            return;
        }
    }

    /**
     * Construct a client for lists
     * @return CS_REST_Lists
     */
    public function segmentClient($segmentId)
    {
        try
        {
            return new CS_REST_Segments($segmentId, $this->_auth);
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error creating a list client: ".$e->getMessage());
            return;
        }
    }

    /**
     * Construct a general client
     * @param $auth
     * @return CS_REST_General
     */
    public function generalClient($auth)
    {
        try
        {
            return new CS_REST_General($auth);
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error creating a general client: ".$e->getMessage());
            return;
        }

    }

    /**
     * Construct a client client
     * @param $campaignMonitorClientId
     * @param $auth
     * @return CS_REST_Clients|void
     */
    public function clientClient($campaignMonitorClientId)
    {
        try
        {
            return new CS_REST_Clients($campaignMonitorClientId, $this->_auth);
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error creating a client client: ".$e->getMessage());
            return;
        }
    }

    /**
     * Get the subscribers from a segment id
     * @param $segmentId
     * @param $addedSince
     * @param $pageNumber
     * @param $pageSize
     * @return bool
     */
    public function getSubscribersFromSegment($segmentId, $addedSince, $pageNumber, $pageSize)
    {
        try
        {
            // Init CM info
            $this->_initCampaignMonitor();
            // Get a segment
            $client = $this->segmentClient($segmentId);
            // Get the subscribers
            $result = $client->get_subscribers($addedSince, $pageNumber, $pageSize);
            if (!$result->was_successful()) {
                // If you receive '121: Expired OAuth Token', refresh the access token
                if ($result->response->Code == 121) {
                    // Refresh the token
                    $this->refreshToken();
                }
                // Make the call again
                $result = $client->get_subscribers($addedSince, $pageNumber, $pageSize);
            }
            return $result;
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error getting subscribers from segment: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Get a subscriber
     * @param $client
     * @param $email
     * @return bool
     */
    public function getSubscriber($client,$email)
    {
        try
        {
            // Get info using the email
            $result = $client->get($email);
            if (!$result->was_successful()) {
                // If you receive '121: Expired OAuth Token', refresh the access token
                if ($result->response->Code == 121) {
                    // Refresh the token
                    $this->refreshToken();
                }
                // Make the call again
                $result = $client->get($email);
            }
            return $result;
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error getting a subscriber: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Unsubscribe
     * @param $email
     * @param null $listId
     * @return bool|void
     */
    public function unsubscribe($email,$listId = null)
    {
        try
        {
            // Init CM info
            $this->_initCampaignMonitor($listId);
            // Get a client
            $client = $this->subscriberClient();
            // Unsubscribe from CM
            $result = $client->unsubscribe($email);
            if (!$result->was_successful()) {
                // If you receive '121: Expired OAuth Token', refresh the access token
                if ($result->response->Code == 121) {
                    // Refresh the token
                    $this->refreshToken();
                }
                // Make the call again
                $result = $client->unsubscribe($email);
            }
            if($result->was_successful()) return true;
            else return false;
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error unsubscribing: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Update a subscriber
     * @param $email
     * @param $customFields
     * @param $new_email
     * @param $name
     * @param null $listId
     * @return CS_REST_Wrapper_Result|void
     */
    public function update($email,$customFields,$new_email,$name,$listId = null)
    {
        try
        {
            // Init CM info
            $this->_initCampaignMonitor($listId);
            // Get a client
            $client = $this->subscriberClient();
            // Update a subscriber
            $result = $client->update($email,
                array(
                    'CustomFields'  =>  $customFields,
                    'EmailAddress'  =>  $new_email,
                    'Name'          =>  $name,
                    'Resubscribe'   =>  true
                )
            );
            if (!$result->was_successful())
            {
                // If you receive '121: Expired OAuth Token', refresh the access token
                if ($result->response->Code == 121)
                {
                    // Refresh the token
                    $this->refreshToken();
                }
                // Make the call again
                $result = $client->update($email,
                    array(
                        'CustomFields'  =>  $customFields,
                        'EmailAddress'  =>  $new_email,
                        'Name'          =>  $name,
                        'Resubscribe'   =>  true
                    )
                );
            }
            return $result;
        }
        catch(Exception $e) {
            $this->_hlp()->log("Error updating: ".$e->getMessage());
            return;
        }
    }

    /**
     * Add a subscriber to CM
     * @param $email
     * @param $name
     * @param $customFields
     * @param null $listId
     * @return CS_REST_Wrapper_Result|void
     */
    public function add($email,$name,$customFields,$listId = null)
    {
        try
        {
            // Init CM info
            $this->_initCampaignMonitor($listId);
            // Get a client
            $client = $this->subscriberClient();
            // Add a new subscriber
            $result = $client->add(array(
                "EmailAddress"  => $email,
                "Name"          => $name,
                "CustomFields"  => $customFields,
                "Resubscribe"   => true
            ));
            if (!$result->was_successful()) {
                // If you receive '121: Expired OAuth Token', refresh the access token
                if ($result->response->Code == 121) {
                    // Refresh the token
                    $this->refreshToken();
                }
                // Make the call again
                $result = $client->add(array(
                    "EmailAddress"  => $email,
                    "Name"          => $name,
                    "CustomFields"  => $customFields,
                    "Resubscribe"   => true
                ));
            }
            return $result;
        } catch(Exception $e) {
            $this->_hlp()->log("Error adding a subscriber ".$e->getMessage());
            return;
        }
    }

    /**
     * Get information of subscriber from CM
     * @param $email
     * @return null
     */
    public function getCMData($email)
    {
        try
        {
            $response = array();
            // Init CM info
            $this->_initCampaignMonitor();
            // Get a client
            $client = $this->subscriberClient();
            // Get the subscriber
            $result = $this->getSubscriber($client,$email);
            // 200 means there is some details coming back
            // We also check if the subscriber is active
            if ($result && $result->http_status_code == 200
                && $result->response->State == 'Active')
            {
                $response['email'] = $result->response->EmailAddress;
                $response['name'] = $result->response->Name;
                $response = $this->_hlp()->handleFullName($response);
                $response['Date'] = substr($result->response->Date,0,10);
                return $this->_hlp()->storeCustomFields($result->response->CustomFields,$response);
            }
        } catch(Exception $e) {
            $this->_hlp()->log("Error get subscriber data ".$e->getMessage());
            return;
        }
    }

    /**
     * Get subscriber status in CM
     * $email subscriber email
     * return null if there is a problem
     *  0 for not found
     *  1 for unsubscribed
     *  2 for subscribed
     * @param $email $email
     * @return int|null
     */
    public function getCMStatus($email)
    {
        try
        {
            // Init CM info
            $this->_initCampaignMonitor();
            // Get a client
            $client = $this->subscriberClient();
            // Get the subscriber
            $result = $this->getSubscriber($client,$email);
            // 200 means there is some details coming back so subscriber is in CM
            if ($result && $result->http_status_code == 200)
            {
                // If active
                if ($result->response->State == 'Active')
                {
                    return 2;
                }
                else
                {
                    return 1;
                }
            }
            // 400 means not found
            elseif ($result && $result->http_status_code == 400)
            {
                return 0;
            }
            return null;

        } catch(Exception $e) {
            $this->_hlp()->log("Error get subscriber status ".$e->getMessage());
            return null;
        }
    }

    /**
     * Get list details
     *
     * @return bool|mixed|void
     */
    public function getListDetails()
    {
        try {
            $retVal = false;
            // Init CM info
            $this->_initCampaignMonitor();
            // Get a list client
            $client = $this->listClient();
            // Get custom field from the list
            $result = $client->get();

            if (!$result->was_successful()) {
                // If you receive '121: Expired OAuth Token', refresh the access token
                if ($result->response->Code == 121) {
                    // Refresh the token
                    $this->refreshToken();
                }
                // Make the call again
                $result = $client->get();
            }
            else
            {
                $retVal = $result->response;
            }
            return $retVal;
        }
        catch(Exception $e)
        {
            $err = sprintf("%s Error getting list details: %s", __METHOD__, $e->getMessage());
            $this->_hlp()->log($err);
            return;
        }
    }

    /**
     * Get the custom fields
     *
     * @return array
     */
    public function getCustomFields()
    {
        try {
            $retVal = false;
            // Init CM info
            $this->_initCampaignMonitor();
            // Get a list client
            $client = $this->listClient();
            // Get custom field from the list
            $result = $client->get_custom_fields();

            if (!$result->was_successful()) {
                // If you receive '121: Expired OAuth Token', refresh the access token
                if ($result->response->Code == 121) {
                    // Refresh the token
                    $this->refreshToken();
                }
                // Make the call again
                $result = $client->get_custom_fields();
            }
            else
            {
                $retVal = $result->response;
            }
            return $retVal;
        }
        catch(Exception $e)
        {
            $err = sprintf("%s Error getting custom field: %s", __METHOD__, $e->getMessage());
            $this->_hlp()->log($err);
            return;
        }
    }

    /**
     *	Refresh the token
     */
    public function refreshToken()
    {
        try {
            // Check if auth type is OAuth
            if ($this->_hlp()->isOAuth())
            {
                // Get the credentials
                $auth = $this->_hlp()->getAuth();

                // Use the REST lib to refresh the token
                $wrap = $this->generalClient($auth);
                list($new_access_token, $new_expires_in, $new_refresh_token) = $wrap->refresh_token();

                // Use stdClass as it's the same type as OG response
                $response = new stdClass;
                $response->access_token = $new_access_token;
                $response->expires_in = $new_expires_in;
                $response->refresh_token = $new_refresh_token;

                $session = Mage::getModel('core/session');
                $session->setData(self::CAMPAIGNMONITOR_SESSION_DATA_KEY, $response);

                // Save $new_access_token, $new_expires_in, and $new_refresh_token
                Mage::getConfig()->saveConfig(self::CAMPAIGNMONITOR_CONFIG_DATA_KEY, serialize($response), 'default', 0);
            }
        } catch(Exception $e) {
            $this->_hlp()->log("Error refreshing the token: ".$e->getMessage());
        }
    }

    /**
     * Get the access token
     * @param $authRedirect
     * @param $code
     * @return CS_REST_Wrapper_Result
     */
    public function getAccessToken($authRedirect,$code)
    {
        try {
            $result = CS_REST_General::exchange_token(
                $this->_hlp()->getClientId(),
                $this->_hlp()->getClientSecret(),
                $authRedirect,
                $code
            );

            if ($result->was_successful()) {
                /* No need to get those data but just in case */
                /*$access_token = $result->response->access_token;
                $expires_in = $result->response->expires_in;
                $refresh_token = $result->response->refresh_token;*/

                return $result->response;
            } else {
                echo 'An error occurred:\n';
                echo $result->response->error.': '.$result->response->error_description."\n";
                return false;
            }
        } catch(Exception $e) {
            $this->_hlp()->log("Error getting the access token: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Get the auth URL
     * @param $authRedirect
     * @return string
     */
    public function getAuthUrl($authRedirect)
    {
        try {
            $authUrl = CS_REST_General::authorize_url(
                $this->_hlp()->getClientId(),
                $authRedirect,
                'ImportSubscribers,ManageLists'
            );
            return $authUrl;
        } catch(Exception $e) {
            $this->_hlp()->log("Error getting the auth URL: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Get the campaign monitor list
     * @param $campaignMonitorClientId
     * @return array|null
     */
    public function getCampaignMonitorLists($campaignMonitorClientId)
    {
        try
        {
            $response = array();
            $response[] = array(
                'value' => NULL,
                'label' => "Default"
            );

            // Init CM info
            $this->_initCampaignMonitor();
            // Get a list client
            $client = $this->clientClient($campaignMonitorClientId);
            // Get the list
            $result = $client->get_lists();

            if($result->was_successful()) {
                foreach($result->response as $list) {
                    $response[] = array(
                        'value' => $list->ListID,
                        'label' => $list->Name
                    );
                }
                return $response;
            }
            else{
                $this->_hlp()->log("Error getting the lists: " . $result->response);
                return null;
            }
        } catch(Exception $e) {
            $this->_hlp()->log("Error getting the lists: " . $e->getMessage());
            return null;
        }
    }

}