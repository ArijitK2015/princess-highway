<?php
require_once 'Mage/Wishlist/controllers/IndexController.php';
class FactoryX_ProductRefresh_WishlistController extends Mage_Wishlist_IndexController
{
	/**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     */
    public function cartAction()
    {
		$params = $this->getRequest()->getParams();
		if(isset($params['isAjax']) && $params['isAjax'] == 1){
			$response = array();
			if (!$this->_validateFormKey()) {
				return $this->_redirect('*/*');
			}
			$itemId = (int) $this->getRequest()->getParam('item');

			/* @var $item Mage_Wishlist_Model_Item */
			$item = Mage::getModel('wishlist/item')->load($itemId);
			if (!$item->getId()) {
				return $this->_redirect('*/*');
			}
			$wishlist = $this->_getWishlist($item->getWishlistId());
			if (!$wishlist) {
				return $this->_redirect('*/*');
			}

			// Set qty
			$qty = $this->getRequest()->getParam('qty');
			if (is_array($qty)) {
				if (isset($qty[$itemId])) {
					$qty = $qty[$itemId];
				} else {
					$qty = 1;
				}
			}
			$qty = $this->_processLocalizedQty($qty);
			if ($qty) {
				$item->setQty($qty);
			}

			/* @var $session Mage_Wishlist_Model_Session */
			$session    = Mage::getSingleton('wishlist/session');
			$cart       = Mage::getSingleton('checkout/cart');

			try {
				$options = Mage::getModel('wishlist/item_option')->getCollection()
						->addItemFilter(array($itemId));
				$item->setOptions($options->getOptionsByItem($itemId));

				$buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
					$this->getRequest()->getParams(),
					array('current_config' => $item->getBuyRequest())
				);

				$item->mergeBuyRequest($buyRequest);
				if ($item->addToCart($cart, true)) {
					$cart->save()->getQuote()->collectTotals();
				}

				$wishlist->save();
				Mage::helper('wishlist')->calculate();
				
				//New Code Here
				$this->loadLayout();
				$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
				$sidebar_block = $this->getLayout()->getBlock('cart_sidebar');
				Mage::register('referrer_url', $this->_getRefererUrl());
				$sidebar = $sidebar_block->toHtml();
				$response['toplink'] = $toplink;
				$response['sidebar'] = $sidebar;
				$response['status'] = 'SUCCESS';
				$response['message'] = 'Your product has been successfully added to your cart';
			} catch (Mage_Core_Exception $e) {
				if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
					$msg = $this->__('This product(s) is currently out of stock');
				} else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
					$msg = $e->getMessage();
				} else {
					$msg = $e->getMessage();
				}
				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add item to shopping cart');
				Mage::logException($e);
			}

			Mage::helper('wishlist')->calculate();

			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		}
		else
		{
			parent::cartAction();
		}
    }
}
