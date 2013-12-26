<?php
class  Mage_Catalog_Block_Product_View_Addtocart extends Mage_Catalog_Block_Product_View_Abstract {

	protected function _construct() {
		$this->addData(
			array(
				'cache_lifetime' => 86400,
				'cache_key' => "addtocart".$this->getRequest()->getRequestUri()
			)
		);
	}
	
	public function getCacheTags() {
		return array(Mage_Catalog_Model_Product::CACHE_TAG);
	}
	
	public function getCacheKey() {
		return "addtocart".$this->getRequest()->getRequestUri();
	}

}
