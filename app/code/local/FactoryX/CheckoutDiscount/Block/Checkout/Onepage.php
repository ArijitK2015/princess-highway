<?php

/**
 * Class FactoryX_CheckoutDiscount_Block_Checkout_Onepage
 */
class FactoryX_CheckoutDiscount_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
	/**
	 * Get 'one step checkout' step data
	 *
	 * @return array
	 */
	public function getSteps()
	{
		$steps = array();
		$stepCodes = $this->_getStepCodes();
	
		if ($this->isCustomerLoggedIn()) {
			$stepCodes = array_diff($stepCodes, array('login'));
		}
	
		foreach ($stepCodes as $step) {
			$steps[$step] = $this->getCheckout()->getStepData($step);
		}
		
		$steps['discount'] = array('label'=>'Discount Code','is_show'=>true);
	
		return $steps;
	}
	
	/**
	 * Get checkout steps codes
	 *
	 * @return array
	 */
	protected function _getStepCodes()
	{
		return array('login', 'billing', 'shipping', 'discount', 'shipping_method', 'payment', 'review');
	}
}