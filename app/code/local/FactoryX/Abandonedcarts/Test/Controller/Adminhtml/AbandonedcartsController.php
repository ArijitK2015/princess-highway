<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Abandonedcarts_Test_Controller_Adminhtml_AbandonedcartsController extends EcomDev_PHPUnit_Test_Case_Controller
{
    public function setUp()
    {
        parent::setUp();

        $this->mockAdminSession();
        $this->disableAdminNotifications();
    }

    /**
     * Build the admin session mock
     */
    protected function mockAdminSession()
    {
        $mockUser = $this->getModelMock('admin/user');
        $mockUser->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $mockSession = $this->getModelMockBuilder('admin/session')
            ->disableOriginalConstructor()
            ->setMethods(array('isLoggedIn', 'getUser', 'refreshAcl', 'isAllowed'))
            ->getMock();
        $mockSession->expects($this->any())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));
        $mockSession->expects($this->any())
            ->method('refreshAcl')
            ->will($this->returnSelf());
        $mockSession->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'admin/user', $mockUser);
        $mockSession->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($mockUser));
        $this->replaceByMock('singleton', 'admin/session', $mockSession);
    }

    /**
     * Disable the admin notification rss feed
     */
    protected function disableAdminNotifications()
    {
        // Disable notification feed during test
        $mockFeed = $this->getModelMockBuilder('adminnotification/feed')
            ->disableOriginalConstructor()
            ->setMethods(array('checkUpdate', 'getFeedData'))
            ->getMock();
        $mockFeed->expects($this->any())
            ->method('checkUpdate')
            ->will($this->returnSelf());
        $mockFeed->expects($this->any())
            ->method('getFeedData')
            ->will($this->returnValue(''));
        $this->replaceByMock('model', 'adminnotification/feed', $mockFeed);
    }

    /**
     * Test whether fake user successfully logged in
     */
    public function testLoggedIn()
    {
        $this->assertTrue(Mage::getSingleton('admin/session')->isLoggedIn());
    }

    /**
     * @test
     * @singleton admin/session
     */
    public function testSendButtonClick()
    {
        $this->markTestIncomplete('incomplete');
        $this->dispatch('adminhtml/system/configuration');
        $this->assertResponseBody('1');
    }
}