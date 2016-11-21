<?php
/**
 * Class FactoryX_Contests_Helper_Data
 */
class FactoryX_Contests_Helper_Data extends Mage_Core_Helper_Abstract
{

    private static $subscribeToCampaignMonitor = true;

    private static $campaignMonitorClientID = '93a76be4d9f1422ecf118d7d42e8cffa';

    private static $defaultMappings = array(
        'name'      => 'Name',
        'email'     => 'Email address'
        /*,
        'mobile'    => 'Mobile',
        'state'     => 'State',
        'title'     => 'Source'
        */
    );

    protected $logFileName = 'factoryx_contests.log';

    /*
     * Recursively searches and replaces all occurrences of search in subject values replaced with the given replace value
     * @param string $search The value being searched for
     * @param string $replace The replacement value
     * @param array $subject Subject for being searched and replaced on
     * @return array Array with processed values
     */

    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @return array
     */
    public function recursiveReplace($search, $replace, $subject)
    {
        if (!is_array($subject))
            return $subject;

        foreach ($subject as $key => $value)
            if (is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif (is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }

    /**
     * @param $n
     * @return int|string
     */
    public function numberSuffix($n)
    {
        $suffix = 'th';
        if(!($n >= 10 && $n < 20))
        {
            $s = array ('st','nd','rd');
            $s = array_key_exists($n % 10 - 1,$s) ? $s[$n % 10 - 1] : 0;
            $suffix = $s ? $s : 'th';
        }
        return $suffix;
    }

    /**
     * @return mixed
     */
    public function getNotFoundRedirectUrl()
    {
        $redirectUrl = Mage::getStoreConfig('contests/options/notfoundredirecturl');

        if (!$redirectUrl)
        {
            $redirectUrl = Mage::helper('core/url')->getHomeUrl();
        }

        return $redirectUrl;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return Mage::getStoreConfig('contests/options/template');
    }

    /**
     * @return array
     */
    public function getSender()
    {
        $sender = array();
        $sender['email'] = Mage::getStoreConfig('contests/options/email');
        $sender['name'] = Mage::getStoreConfig('contests/options/name');

        return $sender;
    }

    /**
     * @param $fields
     * @param null $listID
     * @return bool
     */
    public function subscribeToCampaignMonitor($fields,$listID = NULL)
    {
        // Module relies on Campaign Monitor module
        if (!$listID)   $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
        $session = Mage::getSingleton('core/session');
        $customFields = array();
        $mapping = $this->generateMapping('formfields','campaignmonitor');

        if ($listID)
        {
            try
            {
                // set the data from form to model
                foreach ($fields as $key => $param)
                {
                    if (!isset($mapping[$key])) {
                        // try default mapping
                        if (array_key_exists($key, self::$defaultMappings)) {
                            $customFields[] = array("Key"=>self::$defaultMappings[$key],"Value"=>$param);
                        }
                        else {
                            // this is some information that we won't know how to store in cm, so we log
                            $this->log(
                                sprintf("FactoryX_Contests_Helper_Data: '%s' is not defined in the campaign monitor mapping.", $key)
                            );
                        }
                    }
                    elseif (!empty($param) && !empty($mapping[$key]))
                    {
                        $customFields[] = array("Key"=>$mapping[$key],"Value"=>$param);
                    }
                }

                // Add secure hash
                $customFields[] = array("Key"=>"securehash","Value"=>md5($fields['email'].$apiKey));

                // Add gender
                if ($fields['gender']){
                    $customFields[] = array("Key"=>"Gender","Value"=>$fields['gender']);
                }

                // Add interests for Gorman
                if (strpos(Mage::getBaseUrl(),"gormanshop.com.au") !== false)
                {
                    $customFields[] = array("Key"=>"interests","Value"=>"clothing");
                    $customFields[] = array("Key"=>"interests","Value"=>"homewares");
                    $customFields[] = array("Key"=>"interests","Value"=>"sale");
                }

                if (self::$subscribeToCampaignMonitor) {
                    $result = Mage::helper('campaignmonitor/cm')->add($fields['email'], $fields['name'], $customFields, $listID);

                    if (!$result->was_successful())
                    {
                        $this->log(sprintf("Failed with code %s", $result->http_status_code));
                        $this->log(print_r($result->response, true));
                        return false;
                    }
                    else return true;
                }
                else {
                    return false;
                }
            }
            catch (Exception $e)
            {
                $this->log(sprintf("%s: Error in CampaignMonitor SOAP call: ", __METHOD__, $e->getMessage()));
                $session->addException($e, $this->__('There was a problem with the subscription'));
                return false;
            }
        }
        else
        {
            $this->log("Error: Campaign Monitor API key and/or list ID not set in Magento Newsletter options.");
            return false;
        }
        $this->log("Error: Should not go here.");
        return false;
    }

    /**
     * @param $badlyFormattedDate
     * @return bool|string
     */
    public function getCountdownFormattedEndDate($badlyFormattedDate)
    {
        $result             = [];
        $timestamp          = strtotime($badlyFormattedDate);
        $result['year']     = date('Y', $timestamp);
        $result['month']    = date('n', $timestamp);
        $result['day']      = date('j', $timestamp);
        $result['hours']    = date('H', $timestamp);
        $result['minutes']  = date('i', $timestamp);
        $result['month']--;
        return $result;
    }

    /**
     *
     */
    public function getStates()
    {
        return explode(',',Mage::getStoreConfig('contests/options/location_options'));
    }

    /**
     * @param string $source
     * @param string $destination
     * @return array
     */
    public function generateMapping($source, $destination) {


        $result = array();
        $mappings = $linkedAttributes = unserialize(Mage::getStoreConfig('contests/options/m_to_cm_attributes',
            Mage::app()->getStore()->getStoreId()));

        // check if mappings exist
        if ($mappings) {
            foreach($mappings as $mapping) {
                if (!empty($mapping[$source]) && !empty($mapping[$destination])){
                    $result[$mapping[$source]] = $mapping[$destination];
                }
            }
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getAppId(){
        return Mage::getStoreConfig('contests/facebook/appId');
    }

    /**
     * @return mixed
     */
    public function getAppSecret(){
        return Mage::getStoreConfig('contests/facebook/appSecret');
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
     * @return array|null
     */
    public function getCampaignMonitorLists()
    {
        return Mage::helper('campaignmonitor/cm')->getCampaignMonitorLists(self::$campaignMonitorClientID);
    }

    /**
     * @param $field
     * @param $fieldname
     * @param $minlen
     * @param $maxlen
     * @return bool|string
     */
    public function validateWordCount($field, $fieldname, $minlen, $maxlen)
    {
        // calculation for counting in numbers
        $count = count(explode(' ',$field));

        if($count < $minlen )
        {
            return ($this->__("%s contains too few words (required:%s - %s) (current word count: %s)", $fieldname, $minlen, $maxlen, $count ) );
        }
        // Check maximum string length
        if($count > $maxlen )
        {
            return ($this->__("%s contains too many words (max:%s) (current word count: %s)", $fieldname, $maxlen, $count ) );
        }

        return true;
    }

    /**
     * @param $request
     * @param $formId
     * @return mixed
     */
    public function getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }

    /**
     * @return array
     */
    public function getTransactionalEmailList(){
        $transactional_emails = Mage::getModel('core/email_template')->getCollection();
        $response = array(
            array(
                'value' => -1,
                'label' => "Default Template"
            )
        );
        foreach ($transactional_emails as $email){
            $response[] = array(
                'value' => $email->getData('template_id'),
                'label' => $email->getData('template_code')
            );
        }
        return $response;
    }

    public function getNextId()
    {
        $contest_entity_table = Mage::getSingleton('core/resource')->getTableName('contests/contest');
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $result = $connection->showTableStatus($contest_entity_table);
        $next_contest_id = $result['Auto_increment'];
        return $next_contest_id;
    }

}