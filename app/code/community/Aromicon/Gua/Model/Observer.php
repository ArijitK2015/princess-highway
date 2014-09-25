<?php
class Aromicon_Gua_Model_Observer
{
    public function logCartAdd() {
        if (!Mage::app()->getRequest()->getParam('product', 0)) {
            return;
        }
		
		$productId = Mage::app()->getRequest()->getParam('product');
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addFieldToFilter('entity_id', array($productId))
			->addAttributeToSelect(array('url_key','sku','name','price'))
			->setPageSize(1);				
			
		$product = $collection->getFirstItem();
		
		// Generate the referer category
		// Replace the store URL in the referer URL
		$cleanUrl = str_replace(Mage::getUrl(),'',Mage::helper('core/http')->getHttpReferer());
		// Replace the product url-key in the clean URL
		$refererCategory = str_replace("/" . $product->getUrlKey() . ".html",'',$cleanUrl);
		
		// Search will lead to refererCategory = url-key.html
		if ($refererCategory == $product->getUrlKey() . ".html")
		{
			$refererCategory = "catalogsearch";
		}
		// Wishlist will lead to refererCategory =  wishlist/index/configure/id/product-id
		else if (strpos($refererCategory,"wishlist/index/configure/id/") !== false)
		{
			$refererCategory = "wishlist";
		}
		
		// In the case of the user clicks "continue" in the popup instead of viewing the cart page,
		// Then add another product to its cart
		// There is one product in the session already, we retrieve it
		if ($existingProduct = Mage::getModel('core/session')->getProductToShoppingCart())
		{
			// Generate an array to store several products in session
			$array[$existingProduct->getId()] = $existingProduct;
			$array[$product->getSku()] = new Varien_Object(array(
					'id' => $product->getSku(),
					'qty' => Mage::app()->getRequest()->getParam('qty', 1),
					'name' => $product->getName(),
					'price' => $product->getPrice(),
					'category' => $refererCategory,
				));
			// Set a new session variable using the array and delete the old single product one
			Mage::getModel('core/session')->setProductsToShoppingCart($array);
			Mage::getModel('core/session')->unsProductToShoppingCart();
		}
		// In the case of several products already in the array
		elseif ($existingProducts = Mage::getModel('core/session')->getProductsToShoppingCart())
		{
			// We check if the product has been already added to the cart
			if (array_key_exists($product->getSku(),$existingProducts))
			{
				// If it has, we simply update the quantity
				$orgQty = $existingProducts[$product->getSku()]['qty'];
				$existingProducts[$product->getSku()] = new Varien_Object(array(
					'id' => $product->getSku(),
					'qty' => $orgQty + Mage::app()->getRequest()->getParam('qty', 1),
					'name' => $product->getName(),
					'price' => $product->getPrice(),
					'category' => $refererCategory,
				));
			}
			else
			{
				// Else we add it to the array
				$existingProducts[$product->getSku()] = new Varien_Object(array(
					'id' => $product->getSku(),
					'qty' => Mage::app()->getRequest()->getParam('qty', 1),
					'name' => $product->getName(),
					'price' => $product->getPrice(),
					'category' => $refererCategory,
				));
			}
			// Unset the old value and reset the new value
			Mage::getModel('core/session')->unsProductsToShoppingCart();
			Mage::getModel('core/session')->setProductsToShoppingCart($existingProducts);
		}
		// In the case of first product being added to cart
		// We use the default code
		else
		{
			Mage::getModel('core/session')->setProductToShoppingCart(
				new Varien_Object(array(
					'id' => $product->getSku(),
					'qty' => Mage::app()->getRequest()->getParam('qty', 1),
					'name' => $product->getName(),
					'price' => $product->getPrice(),
					'category' => $refererCategory,
				))
			);
		}
    }
	
	public function logCartRemove(Varien_Event_Observer $observer) {
		$product = $observer->getQuoteItem()->getProduct();
		
        if (!$product->getId()) {
            return;
        }
		
		// In the case of the user removes several products from its cart
		if ($removedProduct = Mage::getModel('core/session')->getProductFromShoppingCart())
		{
			// Generate an array to store several products in session
			$array[$removedProduct->getId()] = $removedProduct;
			$array[$product->getSku()] = new Varien_Object(array(
					'id' => $product->getSku(),
					'qty' => $observer->getQuoteItem()->getQty(),
					'name' => $product->getName(),
					'price' => $product->getPrice(),
					'category' => "cart",
				));
			// Set a new session variable using the array and delete the old single product one
			Mage::getModel('core/session')->setProductsFromShoppingCart($array);
			Mage::getModel('core/session')->unsProductFromShoppingCart();
		}
		// In the case of several products already in the array
		elseif ($removedProducts = Mage::getModel('core/session')->getProductsFromShoppingCart())
		{
			// Else we add it to the array
			$removedProducts[$product->getSku()] = new Varien_Object(array(
				'id' => $product->getSku(),
				'qty' => $observer->getQuoteItem()->getQty(),
				'name' => $product->getName(),
				'price' => $product->getPrice(),
				'category' => $refererCategory,
			));
			// Unset the old value and reset the new value
			Mage::getModel('core/session')->unsProductsFromShoppingCart();
			Mage::getModel('core/session')->setProductsFromShoppingCart($removedProducts);
		}
		// In the case of first product being added to cart
		// We use the default code
		else
		{
		
			Mage::getModel('core/session')->setProductFromShoppingCart(
				new Varien_Object(array(
					'id' => $product->getSku(),
					'qty' => $observer->getQuoteItem()->getQty(),
					'name' => $product->getName(),
					'price' => $product->getPrice(),
					'category' => "cart",
				))
			);
		}
    }
}