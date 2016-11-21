<?php

/**
 * Class FactoryX_XMLFeed_Test_Controller_XMLFeedTest
 */
class FactoryX_XMLFeed_Test_Controller_XMLFeedTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function setUp()
    {
        // Stub response to avoid headers already sent problems
        $stubResponse = new \FactoryX_TestFramework_Controller_HttpResponse();
        Mage::app()->setResponse($stubResponse);

        // Parameter
        Mage::app()->getRequest()->setParam('limit', 1);

        // Use a controller helper
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        // Dispatch the request
        $controllerTestHelper->dispatchGetRequest('xmlfeed', 'index', 'catalog');
    }

    /**
     * Test if the output is XML
     */
    public function testIfXml()
    {
        // Get the header
        $headers = Mage::app()->getResponse()->getHeaders();

        // Test if we're getting XML
        $this->assertContains(['name' => 'Content-Type', 'value' => 'text/xml; charset=UTF-8', 'replace' => ''], $headers);
    }

    /**
     * Test the body output
     */
    public function testBodyOutput()
    {
        // Get the body
        $output = Mage::app()->getResponse()->getBody(true);

        $this->assertArrayHasKey('default', $output);

        $this->assertRegExp('~<product_list ~', $output['default']);
        $this->assertRegExp('~<product ~', $output['default']);
        $this->assertRegExp('~<name>~', $output['default']);
        $this->assertRegExp('~<url>~', $output['default']);
        $this->assertRegExp('~<short_desc>~', $output['default']);
        $this->assertRegExp('~<long_desc>~', $output['default']);
        $this->assertRegExp('~<images>~', $output['default']);
        $this->assertRegExp('~<category>~', $output['default']);
        $this->assertRegExp('~<sub_category>~', $output['default']);
        $this->assertRegExp('~<price>~', $output['default']);
        $this->assertRegExp('~<saleprice>~', $output['default']);
        $this->assertRegExp('~<availability>~', $output['default']);
    }
}