<?php

/**
 * Class FactoryX_CheckoutDiscount_IndexController
 */
class FactoryX_CheckoutDiscount_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Validate ajax request and redirect on failure
	 *
	 * @return bool
	 */
	protected function _expireAjax()
	{
		if (!$this->getOnepage()->getQuote()->hasItems()
			|| $this->getOnepage()->getQuote()->getHasError()
			|| $this->getOnepage()->getQuote()->getIsMultiShipping()) {
			$this->_ajaxRedirectResponse();
			return true;
		}
		$action = $this->getRequest()->getActionName();
		if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
			&& !in_array($action, array('index', 'progress'))) {
			$this->_ajaxRedirectResponse();
			return true;
		}

		return false;
	}


	/**
	 * @return $this
	 */
	protected function _ajaxRedirectResponse()
	{
		$this->getResponse()
			->setHeader('HTTP/1.1', '403 Session Expired')
			->setHeader('Login-Required', 'true')
			->sendResponse();
		return $this;
	}

	/**
	 * Get payment method step html
	 *
	 * @return string
	 */
	protected function _getPaymentMethodsHtml()
	{
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_paymentmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	/**
	 * Get shipping method step html
	 *
	 * @return string
	 */
	protected function _getShippingMethodsHtml()
	{
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_shippingmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	/**
	 * Retrieve shopping cart model object
	 *
	 * @return Mage_Checkout_Model_Cart
	 */
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}

	/**
	 * Get checkout session model instance
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}

	/**
	 * Get current active quote instance
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	protected function _getQuote()
	{
		return $this->_getCart()->getQuote();
	}

	/**
	 * Get one page checkout model
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	public function getOnepage()
	{
		return Mage::getSingleton('checkout/type_onepage');
	}

	/**
	 * Submit the coupon
	 */
	public function submitAction()
	{
		if ($this->_expireAjax()) {
			return;
		}

		if ($this->getRequest()->isPost()) {
			$couponCode = trim($this->getRequest()->getPost('coupon_code'));

			$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
			$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
				->collectTotals()
				->save();
			if ($couponCode == $this->_getQuote()->getCouponCode()) {
				$result = array('error' => 0);
				$this->_getSession()->setStepData('discount', 'complete', true);
			}
			else {
				$result = array('error' => 1, 'message' => $this->__('Coupon code %s is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
			}

			if(!$result['error']) {
				$steps = $this->getOnepage()->getCheckout()->getSteps();
				if ($steps['shipping_method']['is_show']) {
					$this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
					$result['goto_section'] = 'shipping_method';
					$result['update_section'] = array(
						'name' => 'shipping-method',
						'html' => $this->_getShippingMethodsHtml()
					);
				} else {
					$this->getOnepage()->getQuote()->collectTotals();
					$result['goto_section'] = 'payment';
					$result['update_section'] = array(
						'name' => 'payment-method',
						'html' => $this->_getPaymentMethodsHtml()
					);
				}
			}else {
				$result['goto_section'] = 'discount';
			}

			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	/**
	 *
	 */
	public function indexAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * Remove the gift certificate
	 */
	public function removeAction()
	{
		$gc  = $this->getRequest()->getParam('gc');
		$session = Mage::getSingleton('checkout/session');
		$gcs = $session->getQuote()->getGiftcertCode();
		if ($gc && $gcs && stripos($gcs, $gc) !== false) {

			$gcsArr = array();
			foreach (explode(',', $gcs) as $gc1) {
				if (trim($gc1) !== $gc) {
					$gcsArr[] = $gc1;
				}
			}

			$session->getQuote()->setGiftcertCode(join(',', $gcsArr))->save();
		}

		$this->loadLayout();
		$this->renderLayout();
	}
}