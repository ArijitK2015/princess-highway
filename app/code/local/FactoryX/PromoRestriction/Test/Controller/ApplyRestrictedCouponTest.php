<?php

/**
 * Class FactoryX_PromoRestriction_Test_Controller_OnepageSuccessTest
 */
class FactoryX_PromoRestriction_Test_Controller_ApplyRestrictedCouponTest extends \PHPUnit_Framework_TestCase {

    protected $_couponCode = "test";
    protected $_restrictedEmail = "test@factoryx.com.au";
    protected $_ruleId;

    /**
     * @throws Zend_Controller_Exception
     */
    public function setUp()
    {
        // Stub response to avoid headers already sent problems
        $stubResponse = new \FactoryX_TestFramework_Controller_HttpResponse();
        Mage::app()->setResponse($stubResponse);

        // Init a coupon code
        $this->_initCoupon();
    }

    /**
     * Init the cart with a product
     */
    protected function _initCartWithProduct()
    {
        // Init cart
        $cart = Mage::getSingleton('checkout/cart');
        $cart->init();

        /*
        Get enabled, simple products
        use this to get visible products, although the majority of simples are hidden
        ->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
        */
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->setPageSize(1);
        // In stock only
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        // Load the product
        $product = Mage::getModel('catalog/product')->load($products->getFirstItem()->getId());
        $this->assertNotEmpty($product->getSku());
        //echo sprintf("sku: %s\n", $product->getSku());

        // Create the request for the add to cart
        $request = new Varien_Object();
        $params = [
            'product'   => $product->getId(),
            'qty'       => 1
        ];
        $request->setData($params);

        // Add to cart
        $cart->addProduct($product, $request);

        // Save cart
        $cart->save();

        // Flag cart as updated
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    }

    /**
     * Init the coupon
     * @throws Exception
     */
    protected function _initCoupon()
    {
        $websites = Mage::app()->getWebsites();
        $websiteArray = [];
        foreach ($websites as $website) {
            $websiteArray[] = $website->getId();
        }

        /** @var Mage_SalesRule_Model_Rule $coupon */
        $coupon = Mage::getModel('salesrule/rule');

        $coupon->setName("Test")
            ->setDescription("Test")
            ->setCouponCode($this->_couponCode)
            ->setCustomerGroupIds(Mage::getModel('customer/group')->getCollection()->getColumnValues('customer_group_id'))
            ->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
            ->setIsActive(1)
            ->setSimpleAction(Mage_SalesRule_Model_Rule::CART_FIXED_ACTION)
            ->setDiscountAmount(10)
            ->setWebsiteIds($websiteArray);

        $coupon->save();

        $this->_ruleId = $coupon->getRuleId();

    }

    /**
     * Test that the promo is applied
     */
    public function testPromoWithoutRestriction()
    {
        // Init cart
        $this->_initCartWithProduct();

        // Use a controller helper
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        $controllerTestHelper->dispatchPostRequest('checkout', 'cart', 'index');

        // Get the org grand total
        $cart = Mage::getSingleton('checkout/cart');
        $quote = $cart->getQuote();
        $orgGrandTotal = $quote->getGrandTotal();

        // Unflag the total collect
        $quote->setTotalsCollectedFlag(false);

        // Use a controller helper
        Mage::app()->getRequest()->setParam('coupon_code', $this->_couponCode);
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        $controllerTestHelper->dispatchPostRequest('checkout', 'cart', 'couponPost');

        // Test that the coupons are the same
        $this->assertEquals($quote->getCouponCode(), $this->_couponCode);

        // Test the grand total are different
        $this->assertNotEquals($orgGrandTotal, $quote->getGrandTotal());

        // Get the block
        $block = Mage::app()->getLayout()->getBlock('checkout.cart');

        // Test if the correct block was created
        $this->assertNotNull($block);
        $this->assertInstanceOf(Mage_Checkout_Block_Cart::class, $block);
    }

    /**
     * Test that the promo is not applied when there is a restriction
     */
    public function testPromoWithRestriction()
    {
        // Init restriction
        $this->_initRestriction();

        // Init cart
        $this->_initCartWithProduct();

        // Use a controller helper
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        $controllerTestHelper->dispatchPostRequest('checkout', 'cart', 'index');

        // Get the org grand total
        $cart = Mage::getSingleton('checkout/cart');
        $quote = $cart->getQuote();
        $orgGrandTotal = $quote->getGrandTotal();

        // Unflag the total collect
        $quote->setTotalsCollectedFlag(false);

        // Use a controller helper
        Mage::app()->getRequest()->setParam('coupon_code', $this->_couponCode);
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        $controllerTestHelper->dispatchPostRequest('checkout', 'cart', 'couponPost');

        // Test that the coupons are the same
        $this->assertEquals($quote->getCouponCode(), $this->_couponCode);

        // Test the grand total are different
        $this->assertNotEquals($orgGrandTotal, $quote->getGrandTotal());

        // Get the block
        $block = Mage::app()->getLayout()->getBlock('checkout.cart');

        // Test if the correct block was created
        $this->assertNotNull($block);
        $this->assertInstanceOf(Mage_Checkout_Block_Cart::class, $block);
    }

    protected function _initRestriction()
    {
        // remove any restrictions before setting another
        $restriction = Mage::getModel('promorestriction/restriction')->load($this->_ruleId, 'salesrule_id');
        if ($restriction->getId()) {
            $restriction->delete();
        }
        $restriction->setData(
            [
                'salesrule_id'      =>  $this->_ruleId,
                'type'              =>  FactoryX_PromoRestriction_Model_Restriction::RESTRICT_EMAIL,
                'restricted_field'  =>  $this->_restrictedEmail
            ]
        )->save();
    }

    /**
     * Delete the previously created coupon
     */
    protected function _deleteCoupon()
    {
        $coupon = Mage::getResourceModel('salesrule/coupon_collection')
            ->addFieldToSelect('rule_id')
            ->addFieldToFilter('code',$this->_couponCode)
            ->setPageSize(1);

        $salesRule = Mage::getResourceModel('salesrule/rule_collection')
            ->addFieldToFilter('rule_id', $coupon->getFirstItem()->getRuleId())
            ->setPageSize(1);

        $salesRule->getFirstItem()->delete();
    }

    /**
     *
     */
    public function tearDown()
    {
        Mage::app()->getRequest()->setParam('remove', 1);
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        $controllerTestHelper->dispatchPostRequest('checkout', 'cart', 'couponPost');

        // Delete the test coupon
        $this->_deleteCoupon();

        Mage::getSingleton('checkout/session')->clear();
        Mage::getSingleton('customer/session')->clear();
        Mage::getSingleton('core/session')->clear();
    }
}
