<?php

/**
 * Class FactoryX_Westfield_Test_Controller_OnepageSuccessTest
 */
class FactoryX_Westfield_Test_Controller_OnepageSuccessTest extends \PHPUnit_Framework_TestCase {

    /**
     * @throws Zend_Controller_Exception
     */
    public function setUp()
    {
        // Stub response to avoid headers already sent problems
        $stubResponse = new \FactoryX_TestFramework_Controller_HttpResponse();
        Mage::app()->setResponse($stubResponse);

        // Use a controller helper
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);

        // Get the last pending quote made
        $order = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect(['entity_id', 'quote_id', 'increment_id'])
            ->setOrder('entity_id', 'DESC')
            ->setPageSize(1)
            ->getFirstItem();

        // Get the last quote and order id
        $lastQuoteId = $order->getQuoteId();
        $lastOrderId = $order->getId();
        $lastRealOrderId = $order->getIncrementId();

        // Set the last quote id and last order id to the session
        Mage::getSingleton('checkout/session')->setLastSuccessQuoteId($lastQuoteId);
        Mage::getSingleton('checkout/session')->setLastQuoteId($lastQuoteId);
        Mage::getSingleton('checkout/session')->setLastRealOrderId($lastRealOrderId);
        Mage::getSingleton('checkout/session')->setLastOrderId($lastOrderId);

        // Set the parameter so we have the flag set
        Mage::app()->getRequest()->setQuery('source', 'westfield');

        // Set a fake campaign ID
        Mage::app()->getStore()->setConfig('westfield/options/campaign_id', '1');

        // Disable HTTPS it'll fail if you don't
        Mage::app()->getStore()->setConfig('web/secure/use_in_frontend', 0);

        // Dispatch the request
        $controllerTestHelper->dispatchGetRequest('checkout', 'onepage', 'success');
    }

    /**
     * Test the success page
     */
    public function testSuccessPage()
    {
        // Test if module is enabled
        $this->assertTrue((boolean)Mage::helper('westfield')->isEnabled());

        // Get the block
        $block = Mage::app()->getLayout()->getBlock('westfield');

        // Test if the block is a template block
        $this->assertInstanceOf(Mage_Core_Block_Template::class, $block);

        // Test if the block was created
        $this->assertNotNull($block);

        // Get the body
        $output = Mage::app()->getResponse()->getBody(true);

        // Check if body has a default output
        $this->assertArrayHasKey('default', $output);

        // Check if the body contains the image
        $this->assertRegExp('~<img src="https://prf.hn/conversion~', $output['default']);
    }

    /**
     *
     */
    public function tearDown()
    {
        Mage::getSingleton('checkout/session')->clear();
        Mage::getSingleton('core/session')->clear();
        unset($_GET['source']);
    }
}