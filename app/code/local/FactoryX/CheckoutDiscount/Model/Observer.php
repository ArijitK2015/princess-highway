<?php

/**
 * Class FactoryX_CheckoutDiscount_Model_Observer
 */
class FactoryX_CheckoutDiscount_Model_Observer
{
	/**
	 * Apply Unirgy gift certificates
	 * @param Varien_Event_Observer $observer
	 */
	public function applyGiftCert(Varien_Event_Observer $observer)
	{
		if ((string)Mage::getConfig()->getModuleConfig('Unirgy_Giftcert')->active == 'true') {
			$action = $observer->getControllerAction();
			/* @var $hlp Unirgy_Giftcert_Helper_Data */
			$hlp = Mage::helper('ugiftcert');

			$code    = trim($action->getRequest()->getParam('coupon_code'));
			$session = Mage::getSingleton('checkout/session');
			$quote   = $session->getQuote();
			$result = array();
			try {
				if ($hlp->addCertificate($code, $quote)) {
					$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
					$result = array('error' => 0);
					Mage::getSingleton('checkout/session')->setStepData('discount', 'complete', true);
				}
			} catch (Unirgy_Giftcert_Exception_Coupon $gce) {
				$result = array('error' => 1, 'message' => $gce->getMessage());
			} catch (Mage_Core_Exception $e) {
				$result = array('error' => 1, 'message' => $hlp->__("Gift certificate '%s' could not be applied to your order.", $code));
			} catch (Exception $e) {
				$result = array('error' => 1, 'message' => $hlp->__("An error occured"));
			}

			if(!array_key_exists('error', $result) || $result['error']) {
				$steps = Mage::getSingleton('checkout/type_onepage')->getCheckout()->getSteps();
				if ($steps['shipping_method']['is_show']) {
					Mage::getSingleton('checkout/type_onepage')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
					$result['goto_section'] = 'shipping_method';
					$result['update_section'] = array(
						'name' => 'shipping-method',
						'html' => $this->_getShippingMethodsHtml($action)
					);
				} else {
					Mage::getSingleton('checkout/type_onepage')->getQuote()->collectTotals();
					$result['goto_section'] = 'payment';
					$result['update_section'] = array(
						'name' => 'payment-method',
						'html' => $this->_getPaymentMethodsHtml($action)
					);
				}
			}else {
				$result['goto_section'] = 'discount';
			}

			$action->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	/**
	 * Make sure after save billing step it go to vendor step
	 * @param unknown_type $observer
	 */
	public function controller_action_postdispatch_checkout_onepage_saveBilling($observer){
		if(!Mage::getStoreConfig('checkoutdiscount/config/enable')) return;

		$controller = $observer->getData('controller_action');
		$body = Mage::helper('core')->jsonDecode($controller->getResponse()->getBody());
		$directToPayment = ($body['goto_section'] == "payment") ? true : false;

		if ($body['goto_section'] == "shipping_method" || $directToPayment) {
			$body['goto_section'] = "discount";
			$body['update_section'] = array(
				'name' => 'discount',
				'html' => $this->_getVendorHtml($controller)
			);
		}
		if ($directToPayment) {
			$body['allow_sections'] = array('billing','discount');
		} else {
			$body['allow_sections'] = array('billing','shipping','discount');
		}
		$controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($body));
	}
	/**
	 * Make sure after save shipping step it go to vendor step
	 * @param unknown_type $observer
	 */
	public function controller_action_postdispatch_checkout_onepage_saveShipping($observer){
		if (!Mage::getStoreConfig('checkoutdiscount/config/enable')) {
			return;
		}

		$controller = $observer->getData('controller_action');
		$body = Mage::helper('core')->jsonDecode($controller->getResponse()->getBody());
		if ($body['goto_section'] == 'shipping_method') {
			$body['goto_section'] = 'discount';
		}
		$body['update_section'] = array(
			'name' => 'shipping-method',
			'html' => $this->_getVendorHtml($controller)
		);
		$controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($body));
	}

	/**
	 * Get discount step html
	 *
	 * @param $controller
	 * @return string
	 */
	protected function _getVendorHtml($controller)
	{
		$layout = $controller->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_discount');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	/**
	 * Get shipping method step html
	 * @param $controller
	 * @return mixed
	 */
	protected function _getShippingMethodsHtml($controller)
	{
		$layout = $controller->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_shippingmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	/**
	 * Get payment method step html
	 * @param $controller
	 * @return mixed
	 */
	protected function _getPaymentMethodsHtml($controller)
	{
		$layout = $controller->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_paymentmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}
}