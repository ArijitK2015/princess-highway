<?php
/**
 * Class FactoryX_CampaignMonitor_Helper_Data
 */
class FactoryX_CampaignMonitor_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $logFileName = 'factoryx_campaignmonitor.log';

    /**
     * Check if the auth type is OAuth
     * @return bool
     */
    public function isOAuth()
    {
        if (Mage::getStoreConfig('newsletter/campaignmonitor/authentication_type') == "oauth") return true;
        else return false;
    }

    /**
     * Retrieve the API Key
     * @return string
     */
    public function getApiKey()
    {
        return trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
    }

    /**
     * Retrieve the List ID
     * @return string
     */
    public function getListId()
    {
        return trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
    }

    /**
     * Retrieve the Client ID
     * @return string
     */
    public function getClientId()
    {
        return trim(Mage::getStoreConfig('newsletter/campaignmonitor/client_id'));
    }

    /**
     * Retrieve the Client Secret
     * @return string
     */
    public function getClientSecret()
    {
        return trim(Mage::getStoreConfig('newsletter/campaignmonitor/client_secret'));
    }

    /**
     * Get the redirect Url
     * @return string
     */
    public function getRedirectUrl()
    {
        return (Mage::getStoreConfig('newsletter/campaignmonitor/redirect_url') ? trim(Mage::getStoreConfig('newsletter/campaignmonitor/redirect_url')) : "/customer/account/");
    }

    /**
     * @return array|bool
     */
    public function getCampaignMonitorField($field) {
        // get session
        $session = Mage::getSingleton('core/session');

        try {
            $customFields = Mage::helper('campaignmonitor/cm')->getCustomFields();
            //$this->log(sprintf("%s->customFields: %s", __METHOD__, print_r($customFields, true)) );
            $field_option = array();
            if ($customFields && is_array($customFields)) {
                foreach ($customFields as $customField) {
                    if (preg_match("/$field/i", $customField->FieldName)) {
                        foreach ($customField->FieldOptions as $customFieldOption) {
                            $field_option[] = $customFieldOption;
                        }
                        return $field_option;
                    }
                }
            }
            return false;
        }
        catch(Exception $e) {
            $error = sprintf("Error: %s", $e->getMessage());
            $this->log($error);
            if (Mage::app()->getStore()->isAdmin()) {
                $session->addException($e, $this->__($error));
            }
            else {
                $session->addException($e, $this->__('There was a problem with the subscription'));
                $this->_redirectReferer();
            }
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public function getCampaignMonitorStates() {
        // get session
        $session = Mage::getSingleton('core/session');

        try {
            $customFields = Mage::helper('campaignmonitor/cm')->getCustomFields();
            //$this->log(sprintf("%s->customFields: %s", __METHOD__, print_r($customFields, true)) );
            $states = array();
            if ($customFields && is_array($customFields)) {
                foreach ($customFields as $customField) {
                    if (preg_match("/state/i", $customField->FieldName)) {
                        foreach ($customField->FieldOptions as $customFieldOption) {
                            $states[] = $customFieldOption;
                        }
                        return $states;
                    }
                }
            }
            return false;
        }
        catch(Exception $e) {
            $error = sprintf("Error: %s", $e->getMessage());
            $this->log($error);
            if (Mage::app()->getStore()->isAdmin()) {
                $session->addException($e, $this->__($error));
            }
            else {
                $session->addException($e, $this->__('There was a problem with the subscription'));
                $this->_redirectReferer();
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getStores()
    {
        $stores = array();
        $stores['NULL'] = "None selected"; // used for grids
        $storesColl = Mage::getModel('ustorelocator/location')->getCollection();
        foreach($storesColl as $store)
        {
            $stores[$store->getStoreCode()] = $store->getTitle();
        }
        asort($stores);
        return $stores;
    }

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }

    /**
     * Get module config section url in admin configuration
     * @return string
     */
    public function getAdminConfigSectionUrl()
    {
        $url = Mage::getModel('adminhtml/url');
        return $url->getUrl('adminhtml/system_config/edit', array(
            '_current'  => true,
            'section'   => 'newsletter'
        ));
    }

    /**
     * @return mixed
     */
    public function getPreferredBrands()
    {
        return unserialize(Mage::getStoreConfig('newsletter/campaignmonitor/preferred_brands',Mage::app()->getStore()->getStoreId()));
    }

    /**
     * @param $label
     * @return bool
     */
    public function getAssociatedBrandList($label)
    {
        $brands = $this->getPreferredBrands();
        foreach($brands as $brand)
        {
            if ($brand['brand'] == $label) return $brand['list'];
        }
        return false;
    }

    /**
     * @return array|string
     */
    public function getAuth()
    {
        // Campaign Monitor API Credentials
        if ($this->isOAuth())
        {
            $accessToken = Mage::getModel('campaignmonitor/auth')->getAccessToken();
            $refreshToken = Mage::getModel('campaignmonitor/auth')->getRefreshToken();

            return array(
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken
            );
        }
        else
        {
            return $this->getApiKey();
        }
    }

    /**
     * Handle full name correctly
     * @param $params
     * @return mixed
     */
    public function handleFullName($params)
    {
        // fullname handler
        if(array_key_exists('name',$params)
            && !array_key_exists('firstname',$params)
            && !array_key_exists('lastname',$params))
        {
            if (strpos($params['name'],(" ")) > 0)
            {
                $params['firstname'] = substr($params['name'],0,strpos($params['name'],(" ")));
                $params['lastname'] = substr($params['name'],strpos($params['name'],(" "))+1);
            }
            else
            {
                $params['firstname'] = $params['name'];
                $params['lastname'] = "";
            }
        }
        return $params;
    }

    /**
     * Unsubscribe from Magento
     * @param $email
     */
    public function mageUnsubscribe($email)
    {
        Mage::getModel('newsletter/subscriber')
            ->loadByEmail($email)
            ->unsubscribe();
    }

    /**
     * Get the magento status
     * 0 - not found
     * 1 - unsubscribed
     * 2 - subscribed
     * @param $email
     * @return int
     */
    public function getMagentoStatus($email)
    {
        // Get this subscriber from Magento
        $subscriberModel = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
        if ($subscriberModel)
        {
            if ($subscriberModel->isSubscribed())
            {
                return 2;
            }
            else
            {
                return 1;
            }
        }
        else return 0;
    }

    /**
     * Generate the mapping
     * @param $source
     * @param $destination
     * @return array
     */
    public function generateMapping($source,$destination)
    {
        $result = array();
        $mappings = unserialize(Mage::getStoreConfig('newsletter/campaignmonitor/m_to_cm_attributes',Mage::app()->getStore()->getStoreId()));
        foreach($mappings as $mapping)
        {
            if (!empty($mapping[$source]) && !empty($mapping[$destination]))
            {
                // Useful for checkboxes such as interests on Gorman
                $explodedSources = explode(',',$mapping[$source]);
                foreach ($explodedSources as $explodedSource)
                {
                    $result[$explodedSource] = $mapping[$destination];
                }
            }
        }
        return $result;
    }

    /**
     * Store custom fields in a response
     * @param $customFields
     * @param $response
     * @return mixed
     */
    public function storeCustomFields($customFields,$response)
    {
        // We store the details in the response array
        foreach ($customFields as $subscriberDetail)
        {
            if (isset($response[str_replace(' ','',$subscriberDetail->Key)]))
            {
                $response[str_replace(' ','',$subscriberDetail->Key)] .= ','.$subscriberDetail->Value;
            }
            else
            {
                $response[str_replace(' ','',$subscriberDetail->Key)] = $subscriberDetail->Value;
            }
        }
        return $response;
    }

    /**
     * Get the lists
     * @param $params
     * @return array
     */
    public function getLists($params)
    {
        $lists = array();
        if (array_key_exists('brands',$params) && !empty($params['brands']))
        {
            if (is_array($params['brands']))
            {
                if (in_array("all",$params['brands']))
                {
                    $allBrands = $this->getPreferredBrands();
                    foreach ($allBrands as $preferredBrand)
                    {
                        if($preferredBrand['list']=='all') continue;
                        $lists[] = $preferredBrand['list'];
                    }
                }
                else
                {
                    foreach ($params['brands'] as $brandLabel)
                    {
                        if ($brandLabel)
                        {
                            $brandList = $this->getAssociatedBrandList($brandLabel);
                            if ($brandList && !in_array($brandList, $lists))
                            {
                                $lists[] = $brandList;
                            }
                        }
                    }
                }
            }
            else
            {
                if ("all" == $params['brands'])
                {
                    $allBrands = $this->getPreferredBrands();
                    foreach ($allBrands as $preferredBrand)
                    {
                        if($preferredBrand['list']=='all') continue;
                        $lists[] = $preferredBrand['list'];
                    }
                }
                else
                {
                    $brandList = $this->getAssociatedBrandList($params['brands']);
                    if ($brandList && !in_array($brandList, $lists))
                    {
                        $lists[] = $brandList;
                    }
                }
            }
        }

        if (empty($lists))
        {
            $lists[] = $this->getListId();
        }
        return $lists;
    }

    /**
     * Add preferred brands to custom fields
     * @param $customFields
     * @param $params
     * @return array
     */
    protected function _addPreferredBrands($customFields,$params)
    {
        // Check if we need to add the brands
        if (array_key_exists('brands',$params) && !empty($params['brands']))
        {
            // Convert the params to an array if it's not one
            if (!is_array($params['brands'])) $params['brands'] = array($params['brands']);
            // Check for all brands
            if (in_array("all",$params['brands']))
            {
                $allBrands = $this->getPreferredBrands();
                foreach ($allBrands as $preferredBrand)
                {
                    $customFields[] = array("Key" => "PreferredBrands", "Value" => $preferredBrand['brand']);
                }
            }
            else
            {
                foreach ($params['brands'] as $brand)
                {
                    $customFields[] = array("Key" => "PreferredBrands", "Value" => $brand);
                }
            }
        }
        return $customFields;
    }


    /**
     * Add interests to custom fields
     * @param $customFields
     * @param $params
     * @return array
     */
    protected function _addInterests($customFields,$params)
    {
        // Check if we need to add the brands
        if (array_key_exists('interests',$params) && !empty($params['interests']))
        {
            // Convert the params to an array if it's not one
            if (!is_array($params['interests'])) $params['interests'] = array($params['interests']);
            foreach ($params['interests'] as $interest)
            {
                $customFields[] = array("Key" => "interests", "Value" => $interest);
            }
        }

        return $customFields;
    }

    /**
     * Generate custom fields from params and mapping
     * @param $params
     * @param $mapping
     * @return array
     */
    public function generateCustomFields($params, $mapping, $update = false)
    {
        $customFields = array();

        // Add popup source
        if (array_key_exists('popup',$params) && $params['popup'])
        {
            $customFields[] = array("Key" => "Source", "Value" => "popup");
        }

        // Add preferred brands
        $customFields = $this->_addPreferredBrands($customFields,$params);

        // Add interests
        $customFields = $this->_addInterests($customFields,$params);
        if (array_key_exists('interests', $params)) {
            // Unset the interest
            unset($params['interests']);
        }

        // Set the data from form to model
        foreach ($params as $key => $param)
        {
            if (!isset($mapping[$key]))
            {
                // this is some information that we won't know how to store in cm, so we log
                $this->log(sprintf("%s->key '%s' is not defined in the campaign monitor mapping.", __METHOD__, $key));
            }
            elseif (!empty($param) && !empty($mapping[$key]))
            {
                $customFields[] = array(
                    "Key"   => $mapping[$key],
                    "Value" => $param
                );
            }
        }

        if (!$update) {
            // Add the default interest in case it's not an update
            if (!array_key_exists('interests',$params) || empty($params['interests']) ) {
                $defaultInterests = $this->getDefaultInterests();
                if ($defaultInterests && is_array($defaultInterests)) {
                    foreach ($defaultInterests as $interest) {
                        $customFields[] = array(
                            "Key" => "interests",
                            "Value" => $interest
                        );
                    }
                }
            }
        }

        return $customFields;
    }

    /**
     * Get email template for list
     * @param $list
     * @return bool
     */
    public function getTemplate($list)
    {
        $brands = $this->getPreferredBrands();
        foreach($brands as $brand)
        {
            if ($brand['list'] == $list) return $brand['template'];
        }
        return false;
    }

    /**
     * Get email templates from lists
     * @param $lists
     * @return array
     */
    public function getTemplates($lists)
    {
        $templates = array();
        foreach ($lists as $list) {
            $templateId = $this->getTemplate($list);
            if (!$templateId) {
                $templateId = Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_SUCCESS_EMAIL_TEMPLATE);
            }
            if (!in_array($templateId, $templates)) {
                $templates[] = $templateId;
            }
        }
        return $templates;
    }

    /**
     * Get the interests values
     * @return array
     */
    public function getInterests()
    {
        $interests = Mage::getStoreConfig('newsletter/campaignmonitor/interests');
        if ($interests) {
            return explode(',', $interests);
        } else {
            return false;
        }
    }

    /**
     * Get the default interests values
     * @return array
     */
    public function getDefaultInterests()
    {
        $interests = Mage::getStoreConfig('newsletter/campaignmonitor/default_interests');
        if ($interests) {
            return explode(',', $interests);
        } else {
            return false;
        }
    }

}
