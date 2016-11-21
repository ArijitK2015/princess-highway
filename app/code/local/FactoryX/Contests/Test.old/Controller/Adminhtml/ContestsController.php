<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Controller_Adminhtml_ContestsController extends EcomDev_PHPUnit_Test_Case_Controller
{
    const FAKE_USER_ID = 4;

    public function setUp()
    {
        //$this->_fakeLogin();
        parent::setUp();
    }

    public function tearDown()
    {
        $adminSession = Mage::getSingleton('admin/session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());
        parent::tearDown();
    }

    /**
     * Test whether fake user successfully logged in
     */
    public function testLoggedIn()
    {
        $this->markTestIncomplete("To be finished");
        $this->assertTrue((bool)Mage::getSingleton('admin/session')->isLoggedIn());
    }
    /**
     * Test whether logged user is fake
     */
    public function testLoggedUserIsFakeUser()
    {
        $this->markTestIncomplete("To be finished");
        /** @var Mage_Admin_Model_User $user */
        $user = Mage::getSingleton('admin/session')->getData('user');
        $this->assertEquals($user->getId(), self::FAKE_USER_ID);
    }

    /**
     * Logged in to Magento with fake user to test an adminhtml controllers
     */
    protected function _fakeLogin()
    {
        $sessionMock = $this->getModelMockBuilder('admin/session')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $this->replaceByMock('singleton', 'admin/session', $sessionMock);

        $this->_registerUserMock();

        Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
        $session = Mage::getSingleton('admin/session');
        $session->login('raphael.petrini', 'factory99');
    }

    /**
     * Creates a mock object for admin/user Magento Model
     *
     * @return self
     */
    protected function _registerUserMock()
    {
        $user = $this->getModelMock('admin/user');
        $user->expects($this->any())->method('getId')->will($this->returnValue(self::FAKE_USER_ID));
        $this->replaceByMock('model', 'admin/user', $user);
        return $this;
    }
}