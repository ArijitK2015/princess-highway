<?php
require_once(Mage::getBaseDir("lib") . "/createsend/class/services_json.php");
require_once(Mage::getBaseDir("lib") . "/createsend/csrest_subscribers.php");

class FactoryX_Contests_Helper_Data extends Mage_Core_Helper_Abstract
{

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
		
        if ($apiKey && $listID) 
		{
            try 
			{
				$wrap = new CS_REST_Subscribers($listID, $apiKey);
				
				$result = $wrap->add(
					array(
						'EmailAddress' => $fields['email_address'],
						'Name' => sprintf("%s %s", $fields['first_name'], $fields['last_name']),
						'CustomFields' => array(
							array(
								'Key' => 'Mobile',
								'Value' => $fields['mobile_number']
							),
							array(
								'Key' => 'State',
								'Value' => $fields['state']
							),
							array(
								'Key' => 'Source',
								'Value' => $fields['promoCode']
							)				        
						),
						'Resubscribe' => true
					)
				);
				
				
				if (!$result->was_successful()) 
				{
				    $this->log(sprintf("Failed with code %s", $result->http_status_code));
				    $this->log(var_dump($result->response, true));					
				    
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
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}

}