<?php
/**

*/
class FactoryX_ShippedFrom_Helper_Data extends Mage_Core_Helper_Abstract {
    
	protected $logFileName = 'factoryx_shippedfrom.log';
	
	/**
	*/
	public function getStores() {
		$stores = array();
		$stores['NULL'] = "> None selected"; // used for grids
		$storesColl = Mage::getModel('ustorelocator/location')->getCollection();    	
		foreach($storesColl as $store) {
			$stores[$store->getId()] = $store->getTitle();
		}
		asort($stores);
		return $stores;
	}
    
	/**
	get a hash of user ids and names
	
	$currentUser = Mage::getSingleton('admin/session')->getUser();
	*/
	public function getUsers($byName = false) {
		$users = array();
		// used for grids
		if ($byName) {
			$users['NULL'] = "> None selected";
		}
		$usersColl = Mage::getResourceModel('admin/user_collection');
		foreach($usersColl as $user) {
			if ($byName) {
				$users[$user->getName()] = $user->getName();
			}
			else {
				$users[$user->getId()] = $user->getName();
			}
		}
		asort($users);
		return $users;
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
