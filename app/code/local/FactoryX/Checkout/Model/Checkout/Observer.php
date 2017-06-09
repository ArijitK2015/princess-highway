<?php
/**
checkout_controller_onepage_save_shipping_method

Varien_Event_Observer
array(
    'request'   => $this->getRequest(),
    'quote'     => $this->getOnepage()->getQuote()
)
*/
class FactoryX_Checkout_Model_Checkout_Observer {

    const XPATH_CONFIG_GIFT_PRODUCT_SKU = 'sales/gift_options/product_sku';
    const XPATH_CONFIG_GIFT_ALLOW_ORDER= 'sales/gift_options/allow_order';

    protected $testSku = "zzgift";
    protected $sku;

    /**
     * addGiftWrapping
     *
     * adds a simple product to the cart
     *
     * @param Varien_Event_Observer $observer the observer
     */
    public function addGiftWrapping($observer) {
        
        try {
            if (!Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_ALLOW_ORDER, $store = null)) {return;}

            // Mage_Core_Controller_Request_Http
            $request = $observer->getEvent()->getRequest();     		
            $quote = $observer->getEvent()->getQuote();
            
            $postData = $request->getPost();
            //Mage::helper('fx_checkout')->log(sprintf("postData=%s", print_r($postData, true)) );
            
            $allowGiftMessage = 0;
            if (array_key_exists('allow_gift_messages', $postData)) {
                $allowGiftMessage = $postData['allow_gift_messages'];
            }

            if ($allowGiftMessage) {
                $product = $this->getGiftWrapProduct();
                
                //Mage::helper('fx_checkout')->log(sprintf("Mage_Catalog_Model_Product=%s", is_a($product, 'Mage_Catalog_Model_Product')) );
                if ($product && is_a($product, 'Mage_Catalog_Model_Product')) {
                    if (!$this->productFoundInCart($product->getId())) {
                        // add item to cart
                        $quote->addProduct($product, 1)->save();
                        //Mage::helper('fx_checkout')->log(sprintf("add item '%d' to cart, isSalable=%s", $product->getId(), $product->isSalable()) );            
                        //Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                        //$message = sprintf("the '%s' was successfully added to your shopping bag.", $product->getName());
                        //Mage::getSingleton('checkout/session')->addSuccess($message);                
                    }    
                }
                else {
                    Mage::helper('fx_checkout')->log("Error: product not found");
                }
            }
            else {
                $product = $this->getGiftWrapProduct();
                //Mage::helper('fx_checkout')->log("remove if it exists");
                if (is_a($product, 'Mage_Catalog_Model_Product') ) {
                    if ($this->productFoundInCart($product->getId(), $remove = true)) {
                        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                        //$message = sprintf("the '%s' was successfully removed from your shopping bag.", $item->getName());
                        //Mage::getSingleton('checkout/session')->addSuccess($message);                                    
                    }
                }
            }
        }
        catch(Exception $ex) {
            Mage::helper('fx_checkout')->log(sprintf("Error: %s", $ex->getMessage()) );            
        }
    }
    
    /**
    * getGiftWrapProduct()
    *
    * note. loadByAttribute throws 'The stock item for Product is not valid'
    * $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $this->sku);
    *
    * @return Mage_Catalog_Model_Product $product                   
    */
    public function getGiftWrapProduct() {
        $this->sku = Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_PRODUCT_SKU, $store = null);
        $product = null;
        $id = Mage::getModel('catalog/product')->getIdBySku($this->sku);
        if ($id && is_numeric($id)) {
            $product = Mage::getModel('catalog/product')->load($id);
        }
        return $product;
    }
    
    /**
     * productFoundInCart
     *
     * checks whether a product is in the cart
     *
     * @param int $productId the product id to search for
     * @param bool $remove remove the item if its found
     * @return bool $found
     */
    public function productFoundInCart($productId, $remove = false) {
        $session = Mage::getSingleton('checkout/session');
        $items = $session->getQuote()->getAllVisibleItems();
        $found = false;
        // Mage_Sales_Model_Quote_Item
        foreach ($items as $item) {
            if ($item->getProduct()->getId() == $productId) {
                //Mage::helper('fx_checkout')->log(sprintf("found=%s", $item->getName()) );
                if ($remove) {
                    Mage::helper('fx_checkout')->log(sprintf("remove=%s", $item->getName()) );
                    $session->getQuote()->removeItem($item->getId())->save();
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                }
                $found = true;
                break;
            }
        }
        return $found;
    }
}