<?php

/**
 * Class FactoryX_Contests_Test_Controller_ViewContestTest
 */
class FactoryX_Contests_Test_Controller_ViewContestTest extends \PHPUnit_Framework_TestCase {

    /**
     * @throws Zend_Controller_Exception
     */
    public function setUp()
    {
        $this->getConnection()->beginTransaction();

        // Stub response to avoid headers already sent problems
        $stubResponse = new \DigitalPianism_TestFramework_Controller_HttpResponse();
        Mage::app()->setResponse($stubResponse);

        // Use a controller helper
        $controllerTestHelper = new \DigitalPianism_TestFramework_Controller_TestHelper();

        $contestId = $this->_createContest();

        Mage::app()->getRequest()->setParam('id', $contestId);

        // Dispatch the request
        $controllerTestHelper->dispatchGetRequest('contests', 'index', 'view');
    }

    protected function tearDown()
    {
        $this->getConnection()->rollBack();
        DigitalPianism_TestFramework_Helper_Magento::reset();
    }

    /**
     * Test the success page
     */
    public function testShareOnFacebookPage()
    {
        // Get the body
        $output = Mage::app()->getResponse()->getBody(true);

        // Check if the body contains the facebook share
        $this->assertRegExp('~fb-share-button~', $output['default']);
    }

    private function _createContest()
    {
        $data = array(
            'title' =>  'Test',
            'identifier'    =>  'test',
            'status'    =>  1,
            'type'  =>  1,
            'share_on_facebook' =>  1,
            'displayed' =>  1
        );

        $contest = Mage::getModel('contests/contest')->setData($data)->save();

        return $contest->getId();
    }

    private function getConnection()
    {
        /** @var \Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        return $resource->getConnection('default_write');
    }
}