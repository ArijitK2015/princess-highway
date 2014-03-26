<?php
require_once(Mage::getBaseDir("lib") . "/createsend/class/services_json.php");
require_once(Mage::getBaseDir("lib") . "/createsend/csrest_subscribers.php");

class FactoryX_Contests_Helper_Data extends Mage_Core_Helper_Abstract
{

    private static $subscribeToCampaignMonitor = true;

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
	
	/*
	 *
	 */
	public function numberSuffix($n) 
	{
		$suffix = 'th';
		if(!($n >= 10 && $n < 20)) 
		{
			$s = array ('st','nd','rd');
			$s = $s[$n % 10 - 1];
			$suffix = $s ? $s : 'th';
		}
		return $suffix;
	}
	
	/*
	 *
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
	
	/*
	 *
	 */
	public function getTemplate()
	{
		return Mage::getStoreConfig('contests/options/template');
	}
	
	/*
	 *
	 */
	public function getSender()
	{
		$sender = array();
		$sender['email'] = Mage::getStoreConfig('contests/options/email');
		$sender['name'] = Mage::getStoreConfig('contests/options/name');
		
		return $sender;
	}
	
	/*
	 *
	 */
	public function subscribeToCampaignMonitor($fields) 
	{
		// Module relies on Campaign Monitor module
        $apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
		$session = Mage::getSingleton('core/session');
		$customFields = array();
		$mapping = $this->generateMapping('formfields','campaignmonitor');
		
        if ($apiKey && $listID) 
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
				
				if (self::$subscribeToCampaignMonitor) {
    				$wrap = new CS_REST_Subscribers($listID, $apiKey);
    				$result = $wrap->add(
    					array(
    						//'Name' => sprintf("%s %s", $fields['firstname'], $fields['lastname']),
    						'EmailAddress'  => $fields['email'],    						
    						'Name'          => $fields['name'],
    						'CustomFields'  => $customFields,
    						'Resubscribe'   => true
    					)
    				);
    				
    				if (!$result->was_successful()) 
    				{
    				    $this->log(sprintf("Failed with code %s", $result->http_status_code));
    				    $this->log(var_dump($result->response, true));					
    				    
    				}
    			}
    			else {
    			}
            }
            catch (Exception $e) 
			{
                $this->log(sprintf("%s: Error in CampaignMonitor SOAP call: ", __METHOD__, $e->getMessage()));
                $session->addException($e, $this->__('There was a problem with the subscription'));
            }            
        }
        else 
		{
            $this->log("Error: Campaign Monitor API key and/or list ID not set in Magento Newsletter options.");
        }
	}
	
	/**
	 *
	 */
	public function getCountdownFormattedEndDate($badlyFormattedDate)
	{
		$timestamp = strtotime($badlyFormattedDate);
		return date('m/d/Y H:i', $timestamp);
	}
	
	/**
	 *
	 */
	public function getStates()
	{
		$statesArray = array();
		
		// Add the australian states first
		$australianStates = Mage::getModel('directory/region_api')->items('AU');
		foreach ($australianStates as $ozState)
		{
			$statesArray[$ozState['name']] = $ozState['code'];
		}
		
		// Add New Zealand and an extra value
		$statesArray['New Zealand'] = "NZ";
		$statesArray['Other'] = "Other";
		
		return $statesArray;
	}
	
	/**
	@param  string $source 
	@param  string $destination
	*/
	public function generateMapping($source, $destination) {
	    
	    
        $result = array();
        $mappings = $linkedAttributes = @unserialize(Mage::getStoreConfig('contests/options/m_to_cm_attributes',
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
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}

}