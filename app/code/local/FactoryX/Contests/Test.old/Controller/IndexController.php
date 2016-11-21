<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Controller_IndexController extends EcomDev_PHPUnit_Test_Case_Controller
{
    public function testIndexAction()
    {
        $this->getRequest()->setServer('REQUEST_URI',"/contests");
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('contests');
        $this->assertRequestForwarded();
        $this->assertRequestRoute('contests/index/list');
        $this->assertRequestBeforeForwardedRoute('contests/index/index');
        $this->assertLayoutHandleLoaded('contests_index_list');
        $this->assertLayoutBlockCreated('contestList');
        $this->assertLayoutBlockRendered('contestList');
        $this->assertLayoutBlockTypeOf('contestList','contests/contest_list');
    }

    public function testListAction()
    {
        $this->getRequest()->setServer('REQUEST_URI',"/contests");
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('contests');
        $this->assertRequestRoute('contests/index/list');
        $this->assertLayoutHandleLoaded('contests_index_list');
        $this->assertLayoutBlockCreated('contestList');
        $this->assertLayoutBlockRendered('contestList');
        $this->assertLayoutBlockTypeOf('contestList','contests/contest_list');
    }

    /**
     * @test
     * @loadFixture controller.yaml
     */
    public function testViewAction()
    {
        $this->getRequest()->setServer('REQUEST_URI',"/contests/index/view");
        $this->getRequest()->setParam('id',1);
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('contests/index/view/id/1/', array('_store' => 'default'));
        $this->assertRequestRoute('contests/index/view');
        $this->assertLayoutHandleLoaded('contests_index_view');
        $this->assertLayoutBlockCreated('contestForm');
        $this->assertLayoutBlockRendered('contestForm');
        $this->assertLayoutBlockTypeOf('contestForm','contests/contest');
    }

    /**
     * @test
     * @loadFixture controller.yaml
     */
    public function testTermsAction()
    {
        $this->getRequest()->setServer('REQUEST_URI',"/contests/index/terms");
        $this->getRequest()->setParam('id',1);
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('contests/index/terms/id/1/', array('_store' => 'default'));
        $this->assertRequestRoute('contests/index/terms');
        $this->assertLayoutHandleLoaded('contests_index_terms');
        $this->assertLayoutBlockCreated('contest_terms');
        $this->assertLayoutBlockRendered('contest_terms');
        $this->assertLayoutBlockTypeOf('contest_terms','contests/contest_terms');
        $this->assertResponseBodyContains('PHPUnit Test - Terms and Conditions');
        $this->assertResponseBodyContains('Bla Bla Bla');
    }

    /**
     * @test
     * @loadFixture controller.yaml
     */
    public function testThankYouAction()
    {
        $this->getRequest()->setServer('REQUEST_URI',"/contests/index/thankyou");
        $this->getRequest()->setParam('id',1);
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('contests/index/thankyou/id/1/', array('_store' => 'default'));
        $this->assertRequestRoute('contests/index/thankyou');
        $this->assertLayoutHandleLoaded('contests_index_thankyou');
        $this->assertLayoutBlockCreated('contest_thankyou');
        $this->assertLayoutBlockRendered('contest_thankyou');
        $this->assertLayoutBlockTypeOf('contest_thankyou','contests/contest_thankyou');
        $this->assertResponseBodyContains('Thank you and good luck!!!');
    }

    /**
     * @test
     * @loadFixture controller.yaml
     * @loadExpectation controller.yaml
     */
    public function testPostAction()
    {
        // Good form
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost('name','PHPUnit Name');
        $this->getRequest()->setPost('contest_id',1);
        $this->getRequest()->setPost('email','phpunit@factoryx.com.au');
        $this->getRequest()->setPost('mobile','0695197773');
        $this->getRequest()->setPost('state','VIC');
        $this->getRequest()->setPost('terms','on');
        $this->dispatch('contests/index/post');
        $this->assertRequestRoute('contests/index/post');
        $referrer = Mage::getModel('contests/referrer')->loadByEmailAndContest('phpunit@factoryx.com.au',1);
        $this->assertEquals($referrer->getName(),$this->expected("1-1")->getName());

        // Reset
        $this->reset();

        // Missing fields form
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost('name','PHPUnit Name 2');
        $this->getRequest()->setPost('contest_id',1);
        $this->getRequest()->setPost('email','phpunit2@factoryx.com.au');
        $this->dispatch('contests/index/post');
        $this->assertRequestRoute('contests/index/post');
        $referrer = Mage::getModel('contests/referrer')->loadByEmailAndContest('phpunit2@factoryx.com.au',1);
        $this->assertNotEquals($referrer->getName(),$this->expected("1-2")->getName());
    }
}