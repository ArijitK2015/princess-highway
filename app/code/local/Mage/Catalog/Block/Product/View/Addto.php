<?php
class Mage_Catalog_Block_Product_View_Addto extends Mage_Catalog_Block_Product_View_Abstract {

	protected function _construct() {
		$this->addData(
			array(
				'cache_lifetime' => 86400,
				'cache_key' => "addto".$this->getRequest()->getRequestUri()
			)
		);
		Mage::log($this->getRequest()->getRequestUri().$this->getName());
	}
	
	public function getCacheTags() {
		return array(Mage_Catalog_Model_Product::CACHE_TAG);
	}
	
	public function getCacheKey() {
		return "addto".$this->getRequest()->getRequestUri();
	}
    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        $sendToFriendModel = Mage::registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }

}
