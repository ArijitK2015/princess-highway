<?php

class FactoryX_ConditionalAgreement_Test_Helper_DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidConfigurationAndValidProductsProvider
     */
    public function testWithInvalidConfigurationAndValidProducts($config, Mage_Catalog_Model_Product $product)
    {
        $this->_setConfiguration($config);

        $this->_addProductToCart($product);

        $validagreements = Mage::helper('conditionalagreement')->getRequiredAgreements();

        $this->assertEquals($validagreements, false);
    }

    public function invalidConfigurationAndValidProductsProvider()
    {
        return array(
            'disabled magento configuration'    =>  array(
                'config'  =>  array(
                    'checkout/options/enable_agreements'    =>  0,
                    'conditionalagreement/options/enabled'    =>  1,
                ),
                'product'   =>  $this->_getValidProduct()
            ),
            'disabled module configuration'    =>  array(
                'config'  =>  array(
                    'checkout/options/enable_agreements'    =>  1,
                    'conditionalagreement/options/enabled'    =>  0,
                ),
                'product'   =>  $this->_getValidProduct()
            ),
            'disabled both configurations'    =>  array(
                'config'  =>  array(
                    'checkout/options/enable_agreements'    =>  0,
                    'conditionalagreement/options/enabled'    =>  0,
                ),
                'product'   =>  $this->_getValidProduct()
            )
        );
    }

    /**
     * @dataProvider validConfigurationAndValidProductsProvider
     */
    public function testWithValidConfigurationAndValidProducts($config, Mage_Catalog_Model_Product $product)
    {
        $this->_setConfiguration($config);

        $this->_addProductToCart($product);

        $validagreements = Mage::helper('conditionalagreement')->getRequiredAgreements();

        $this->assertNotEquals($validagreements->getSize(), 0);
    }

    public function validConfigurationAndValidProductsProvider()
    {
        return array(
            'valid configuration and valid product'    =>  array(
                'config'  =>  array(
                    'checkout/options/enable_agreements'    =>  1,
                    'conditionalagreement/options/enabled'    =>  1,
                ),
                'product'   =>  $this->_getValidProduct()
            )
        );
    }

    /**
     * @dataProvider validConfigurationAndInvalidProductsProvider
     */
    public function testWithValidConfigurationAndInvalidProducts($config, Mage_Catalog_Model_Product $product)
    {
        $this->_setConfiguration($config);

        $this->_addProductToCart($product);

        $validagreements = Mage::helper('conditionalagreement')->getRequiredAgreements();

        $this->assertEquals($validagreements->getSize(), 0);
    }

    public function validConfigurationAndInvalidProductsProvider()
    {
        return array(
            'valid configuration and invalid product'    =>  array(
                'config'  =>  array(
                    'checkout/options/enable_agreements'    =>  1,
                    'conditionalagreement/options/enabled'    =>  1,
                ),
                'product'   =>  $this->_getInvalidProduct()
            )
        );
    }

    protected function setUp()
    {
        $this->getConnection()->beginTransaction();
        $this->_deletePreviousAgreements();
        // Create a salesrule
        $id = $this->_createSalesRuleWithCategoryCondition();
        $agreement = $this->_createAgreement();
        Mage::app()->getStore()->setConfig('conditionalagreement/options/enabled', 1);
        Mage::app()->getStore()->setConfig('conditionalagreement/options/agreement', $agreement);
        Mage::app()->getStore()->setConfig('checkout/options/enable_agreements', 1);
        Mage::app()->getStore()->setConfig('conditionalagreement/options/condition', $id);
    }

    protected function tearDown()
    {
        $this->getConnection()->rollBack();
        DigitalPianism_TestFramework_Helper_Magento::reset();
    }

    private function getConnection()
    {
        /** @var \Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        return $resource->getConnection('default_write');
    }

    private function _setConfiguration($config)
    {
        foreach ($config as $key => $value) {
            Mage::app()->getStore()->setConfig($key, $value);
        }
    }

    private function _deletePreviousAgreements()
    {
        $collection = Mage::getResourceModel('checkout/agreement_collection');
        foreach ($collection as $agreement) {
            $agreement->delete();
        }
    }
    
    private function _createSalesRuleWithCategoryCondition()
    {
        // Create the coupon and save it
        $coupon = Mage::getModel('salesrule/rule');

        $data = array(
            'name'          =>  "Test",
            'coupon_code'          =>  "test",
            'coupon_type'          =>  "2",
            'is_active'          =>  "1",
            'uses_per_coupon'          =>  1000,
            'uses_per_customer'          =>  1000,
            'customer_group_ids'          =>  array(0),
            'stop_rules_processing'          =>  "0",
            'product_ids'          =>  "",
            'is_advanced'          =>  "0",
            'discount_qty'          =>  "0",
            'discount_step'          =>  "0",
            'simple_free_shipping'          =>  "0",
            'apply_to_shipping'          =>  "0",
            'is_rss'          =>  "0",
            'website_ids'          =>  array(1),
            'conditions'    => array(
                "1" =>  array(
                    "type"  =>  "salesrule/rule_condition_combine",
                    "aggregator"    =>  "all",
                    "value" =>  "1",
                    "new_child" =>  ""
                ),
                "1--1"  =>  array(
                    "type"  =>  "salesrule/rule_condition_product_subselect",
                    "aggregator"    =>  "all",
                    "attribute"    =>  "qty",
                    "operator" =>  ">=",
                    "value" =>  "1",
                    "new_child" =>  ""
                ),
                "1--1--1"   =>  array(
                    "type"  =>  "salesrule/rule_condition_product",
                    "attribute" =>  "category_ids",
                    "operator"  =>  "==",
                    "value" =>  $this->_getCategoryIdFromProduct($this->_getSimpleProduct())
                )
            )
        );

        $coupon->loadPost($data);
        $coupon->save();

        return $coupon->getId();
    }

    private function _createAgreement()
    {
        $model = Mage::getModel('checkout/agreement');
        $model->setData(array(
            "name"  =>  "Test",
            "content"   =>  "Test",
            "checkbox_text" =>  "Test",
            "is_active" =>  1,
            "stores"    =>  array(1)
        ))->save();

        return $model->getId();
    }

    private function _getCategoryIdFromProduct(Mage_Catalog_Model_Product $product)
    {
        return current($product->getCategoryIds());
    }

    private function _getSimpleProduct($excludedCategory = null)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel("catalog/product_collection")
            ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addAttributeToFilter('visibility', array('neq'    =>  Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE));

        if ($excludedCategory) {
            $productCollection->getSelect()->join(array('cats' => 'catalog_category_product'), 'cats.product_id = e.entity_id');
            $productCollection->getSelect()->where('cats.category_id != ?',$excludedCategory);
        }

        $productCollection->setPageSize(1);

        Mage::getSingleton('cataloginventory/stock')
            ->addInStockFilterToCollection($productCollection);

        $product = $productCollection->getFirstItem();

        return $product;
    }

    private function _getValidProduct()
    {
        return $this->_getSimpleProduct();
    }

    private function _getInvalidProduct()
    {
        $validProduct = $this->_getValidProduct();

        $excludedCategory = $this->_getCategoryIdFromProduct($validProduct);

        $invalidProduct = $this->_getSimpleProduct($excludedCategory);

        return $invalidProduct;
    }

    private function _addProductToCart(Mage_Catalog_Model_Product $product)
    {
        $product = Mage::getModel('catalog/product')
            ->setStoreId(
                Mage::app()
                    ->getStore()
                    ->getId()
            )
            ->load($product->getEntityId());

        $cart = Mage::getSingleton('checkout/cart');
        $request = new Varien_Object();
        $request->setData(array('product' => $product->getId(), 'qty' => 1));
        $cart->addProduct($product, $request);
        $cart->save();
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    }
}