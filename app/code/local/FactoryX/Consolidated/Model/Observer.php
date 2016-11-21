<?php
/**

 */
class FactoryX_Consolidated_Model_Observer {

    /**
    copies attribute val from product to quote item

    @param $observer Varien_Event_Observer
    */
	public function salesQuoteItemSetAttribute($observer) {

		if (!Mage::helper('fxcons')->isEnabled()) {
		    //Mage::helper('fxcons')->log(sprintf("%s->%s disabled", __METHOD__, get_class($this)));
		    return $this;
		}

		// @var $eavConfig Mage_Eav_Model_Config
		$eavConfig = Mage::getModel('eav/config');

		// @var $quoteItem Mage_Sales_Model_Quote_Item
		$quoteItem = $observer->getQuoteItem();

		// @var $product Mage_Catalog_Model_Product
		$product = $observer->getProduct();
		if (!$product || !$product->getSku()) {
			//Mage::helper('fxcons')->log(sprintf("no product!"));
			return $this;
		}

		// Load product
		$product = Mage::getModel('catalog/product')->load($product->getId());

		$consolidatedAttr = Mage::helper('fxcons')->getConsolidatedAttributes();

		foreach($consolidatedAttr as $attribute) {
			$attributes = $eavConfig->getEntityAttributeCodes(Mage_Catalog_Model_Product::ENTITY, $product);

			if (in_array($attribute, $attributes)) {

				$val = $product->getData($attribute);
				$attributes = $eavConfig->getEntityAttributeCodes(Mage_Catalog_Model_Product::ENTITY, $quoteItem);

				if (in_array($attribute, $attributes)) {
				    // pre_order requires a date check on the product
				    if (preg_match("/pre_order/i", $attribute)) {
				        // check date
				        $availableDate = $product->getData('available_date');
				        //Mage::helper('fxcons')->log(sprintf("%s->availableDate=%s", __METHOD__, $availableDate));
					    if (
					        !empty($availableDate)
					        &&
					        strtotime($availableDate) > Mage::getModel('core/date')->timestamp(time())
                        ) {
						    //Mage::helper('fxcons')->log(sprintf("%s->product not available [%s|%s]", __METHOD__, $availableDate, strtotime($availableDate)));
						    $val = true;
                            //Mage::helper('fxcons')->log(sprintf("%s->copy attribute '%s':'%s' to quote item", __METHOD__, $attribute, $val));
						    $quoteItem->setData($attribute, true);
						}
					}
					// all other attributes
					else {
					    //Mage::helper('fxcons')->log(sprintf("%s->copy attribute '%s':'%s' to quote item", __METHOD__, $attribute, $val));
						$quoteItem->setData($attribute, $val);
					}

				}
				else {
					//Mage::helper('fxcons')->log(sprintf("quote item doesn't have attribute '%s'", $attribute), Zend_Log::WARN);
				}

			}
			else {
				//Mage::helper('fxcons')->log(sprintf("product '%s' doesn't have attribute '%s'", $product->getSku(), $attribute), Zend_Log::WARN);
			}

		}

		return $this;
	}

	/**
	 * @param Varien_Event_Observer $observer
	 */
	public function validateConsolidatedAttributesOnAttrSave(Varien_Event_Observer $observer)
	{
		// Get the controller action
		$controllerAction = $observer->getEvent()->getControllerAction();

		// Get the request
		$request = $controllerAction->getRequest();

		// Get the attributes data
		$attributesData = $request->getParam('attributes');

		if (!$this->_validateConsolidatedAttributes($attributesData)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fxcons')->__('Products cannot be both online only and pre orders.'));
			Mage::app()->getResponse()->setRedirect($controllerAction->getUrl('*/catalog_product/'));
			Mage::app()->getResponse()->sendResponse();
			$controllerAction->setFlag($request->getActionName(),Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 */
	public function validateConsolidatedAttributesOnProdSave(Varien_Event_Observer $observer)
	{
		// Get the controller action
		$controllerAction = $observer->getEvent()->getControllerAction();

		// Get the request
		$request = $controllerAction->getRequest();

		// Get the attributes data
		$attributesData = $request->getParam('product');

		if (!$this->_validateConsolidatedAttributes($attributesData)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fxcons')->__('Products cannot be both online only and pre orders.'));
			Mage::app()->getResponse()->setRedirect($controllerAction->getUrl('*/catalog_product/edit',array('id' => $request->getParam('id'))));
			Mage::app()->getResponse()->sendResponse();
			$controllerAction->setFlag($request->getActionName(),Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,true);
		}
	}

	/**
	 * Validate the consolidated attributes
	 * @param $attributesData
	 * @return bool
	 */
	protected function _validateConsolidatedAttributes($attributesData)
	{
		// Check whether
		// - online_only is set to Yes and pre_order is set to Yes
		// - online_only is set to Yes and an available_date is provided
		if (array_key_exists('online_only',$attributesData)
			&& $attributesData['online_only']
			&& (
				(array_key_exists('pre_order',$attributesData) && $attributesData['pre_order'])
				|| (array_key_exists('available_date',$attributesData) && $attributesData['available_date'])
			))
		{
			return false;
		}
		return true;
	}

}