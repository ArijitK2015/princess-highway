<?php
/**

*/
class FactoryX_ShippedFrom_Helper_Data extends Mage_Core_Helper_Abstract {
    
	protected $logFileName = 'factoryx_shippedfrom.log';
	
	public $stores = array();
	public $users = array();
	
	public $byName = false;
	
	public function generateStoresArray($args)
	{
		$this->stores[$args['row']['location_id']] = $args['row']['title'];
	}
	
	public function generateUsersArray($args)
	{
		if ($this->byName) {
			$this->users[$args['row']['username']] = $args['row']['username'];
		}
		else {
			$this->users[$args['row']['user_id']] = $args['row']['username'];
		}
	}
	
	/**
	*/
	public function getStores() {
		$this->stores['NULL'] = "> None selected"; // used for grids
		$storesColl = Mage::getModel('ustorelocator/location')->getCollection();    	
		// Call iterator walk method with collection query string and callback method as parameters
		// Has to be used to handle massive collection instead of foreach
		Mage::getSingleton('core/resource_iterator')->walk($storesColl->getSelect(), array(array($this, 'generateStoresArray')));

		asort($this->stores);
		return $this->stores;
	}
    
	/**
	get a hash of user ids and names
	
	$currentUser = Mage::getSingleton('admin/session')->getUser();
	*/
	public function getUsers($byName = false) {
		$this->byName = $byName;
	
		// used for grids
		if ($this->byName) {
			$this->users['NULL'] = "> None selected";
		}
		$usersColl = Mage::getResourceModel('admin/user_collection');
		
		// Call iterator walk method with collection query string and callback method as parameters
		// Has to be used to handle massive collection instead of foreach
		Mage::getSingleton('core/resource_iterator')->walk($usersColl->getSelect(), array(array($this, 'generateUsersArray')));

		asort($this->users);
		return $this->users;
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
