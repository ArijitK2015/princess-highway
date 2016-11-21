<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class FactoryX_ProductRefresh_AddController extends Mage_Checkout_CartController
{
	public function addAction()
	{
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		if(isset($params['isAjax']) && $params['isAjax'] == 1){
			$response = array();
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
					    array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();
				$related = $this->getRequest()->getParam('related_product');

				/**
				 * Check product availability
				 */
				if (!$product || !$product->isSaleable()) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}

				$cart->save();

				$this->_getSession()->setCartWasUpdated(true);

				/**
				 * @todo remove wishlist observer processAddToCart
				 */
				Mage::dispatchEvent('checkout_cart_add_product_complete',
				    array(
				        'product'   => $product,
				        'request'   => $this->getRequest(),
				        'response'  => $this->getResponse()
                    )
				);

                $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                $response['status'] = 'SUCCESS';
                $response['message'] = $message;
                //New Code Here
                $this->loadLayout();
                $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                $sidebar_block = $this->getLayout()->getBlock('cart_sidebar');
                Mage::register('referrer_url', $this->_getRefererUrl());
                if ($sidebar_block)
                {
                    $sidebar = $sidebar_block->toHtml();
                    $response['sidebar'] = $sidebar;
                }
                $response['toplink'] = $toplink;
                $response['bag'] = array(
                    "JustAdded" => array(
                        'qty'   => $params['qty'],
                        'title' => $product->getName(),
                        'attributes' => isset($params['super_attribute'])? $params['super_attribute']: array(),
                    )
                );
				
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message.'<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				Mage::logException($e);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}else{
			return parent::addAction();
		}
	}
}
