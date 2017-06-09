<?php

/**
 * Class FactoryX_PromoRestriction_Test_Observer_ObserverTest
 */
class FactoryX_PromoRestriction_Test_Observer_ObserverTest extends \PHPUnit_Framework_TestCase {

    /**
     * Test the observer when no source is specified
     */
    public function testWithoutSource()
    {
        // Stub response to avoid headers already sent problems
        $stubResponse = new \FactoryX_TestFramework_Controller_HttpResponse();
        Mage::app()->setResponse($stubResponse);

        // Use a controller helper
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        // Dispatch the request
        $controllerTestHelper->dispatchGetRequest('index', 'index', 'index');

        // Get the session data
        $sessionData = Mage::getSingleton('core/session')->getData();

        // Test that the session does not contain the PromoRestriction flag
        //$this->assertNotContains(['blah' => true], $sessionData);

        // Clear the session
        Mage::getSingleton('core/session')->clear();
    }

    /**
     * Test the observer when the wrong source is specified
     * @throws Zend_Controller_Exception
     */
    public function testWithWrongSource()
    {
        // Stub response to avoid headers already sent problems
        $stubResponse = new \FactoryX_TestFramework_Controller_HttpResponse();
        Mage::app()->setResponse($stubResponse);

        // Set the parameter
        Mage::app()->getRequest()->setQuery('source', 'wrongsource');

        // Use a controller helper
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        // Dispatch the request
        $controllerTestHelper->dispatchGetRequest('index', 'index', 'index');

        // Get the session data
        $sessionData = Mage::getSingleton('core/session')->getData();

        // Test that the session does not contain the PromoRestriction flag
        //$this->assertNotContains(['blah' => true], $sessionData);

        // Clear the session
        Mage::getSingleton('core/session')->clear();
    }

    /**
     * Test the observer when the right source is specified
     * @throws Zend_Controller_Exception
     */
    public function testWithRightSource()
    {
        // Stub response to avoid headers already sent problems
        $stubResponse = new \FactoryX_TestFramework_Controller_HttpResponse();
        Mage::app()->setResponse($stubResponse);

        // Set the parameter
        Mage::app()->getRequest()->setQuery('source', 'blah');

        // Use a controller helper
        $controllerTestHelper = new \FactoryX_TestFramework_Helper_ControllerTestHelper($this);
        // Dispatch the request
        $controllerTestHelper->dispatchGetRequest('index', 'index', 'index');

        // Get the session data
        $sessionData = Mage::getSingleton('core/session')->getData();

        // Test that the session does not contain the PromoRestriction flag
        // $this->assertContains(['blah' => true], $sessionData);

        // Clear the session
        Mage::getSingleton('core/session')->clear();
    }
}