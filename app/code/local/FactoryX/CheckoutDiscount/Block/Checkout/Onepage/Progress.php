<?php

/**
 * Class FactoryX_CheckoutDiscount_Block_Checkout_Onepage_Progress
 */
class FactoryX_CheckoutDiscount_Block_Checkout_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress
{
	/**
	 * Get checkout steps codes
	 *
	 * @return array
	 */
	protected function _getStepCodes()
	{
		return Mage::getStoreConfig('checkoutdiscount/config/enable')?array('login', 'billing', 'shipping', 'discount', 'shipping_method', 'payment', 'review'):parent::_getStepCodes();
	}
	
	/**
	 * Get is discountcode step completed.
	 *
	 * @return bool
	 */
	public function isDiscountStepComplete()
	{
		$stepsRevertIndex = array_flip($this->_getStepCodes());
	
		$toStep = $this->getRequest()->getParam('toStep');

		if (array_key_exists('discount',$stepsRevertIndex) && array_key_exists($toStep,$stepsRevertIndex))
		{
			if ($stepsRevertIndex['discount'] >= $stepsRevertIndex[$toStep]) {
				return false;
			}
			else return true;
		}
		else return false;

	}
	
}