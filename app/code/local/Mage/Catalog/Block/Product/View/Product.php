<?php
class Mage_Catalog_Block_Product_View_Product extends Mage_Catalog_Block_Product_View {

	protected function _construct() {
		$this->addData(
			array(
				'cache_lifetime' => 86400,
				'cache_key' => "product".$this->getRequest()->getRequestUri()
			)
		);
		Mage::log($this->getRequest()->getRequestUri().$this->getName());
	}
	
	public function getCacheTags() {
		return array(Mage_Catalog_Model_Product::CACHE_TAG);
	}
	
	public function getCacheKey() {
		return "product".$this->getRequest()->getRequestUri();
	}
}
