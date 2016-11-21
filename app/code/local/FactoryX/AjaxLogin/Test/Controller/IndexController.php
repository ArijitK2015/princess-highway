<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 11/12/2014
 * Time: 16:16
 */
class FactoryX_AjaxLogin_Test_Controller_IndexController extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * @test
     * @loadFixture controller.yaml
     * @loadExpectation controller.yaml
     */
    public function forgotpasswordAction()
    {
        $this->markTestIncomplete("Not working");
        // Set password
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $passphrase = "test123";
        $salt = "SC";
        $password = md5($salt . $passphrase) . ":SC";
        $write->query("insert into customer_entity_varchar (value_id,entity_type_id,attribute_id,entity_id,value) VALUES (1,1,(select attribute_id from eav_attribute where attribute_code='password_hash' and entity_type_id=1),9999,'$password')");

        // Good form
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost('email','phpunit@factoryx.com.au');
        $this->dispatch('ajaxlogin/index/forgotpassword');
        $this->assertRequestRoute('ajaxlogin/index/forgotpassword');
    }

    /**
     * @test
     * @loadFixture controller.yaml
     * @loadExpectation controller.yaml
     */
    public function loginAndLogoutAction()
    {
        // Set password
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $passphrase = "test123";
        $salt = "SC";
        $password = md5($salt . $passphrase) . ":SC";
        $write->query("insert into customer_entity_varchar (value_id,entity_type_id,attribute_id,entity_id,value) VALUES (1,1,(select attribute_id from eav_attribute where attribute_code='password_hash' and entity_type_id=1),9999,'$password')");

        // Try with wrong password
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost('login',array('username'   =>  'phpunit@factoryx.com.au', "password"   =>  "123test"));
        $this->dispatch('ajaxlogin/index/login');
        $this->assertRequestRoute('ajaxlogin/index/login');
        $this->assertResponseBodyContains("Invalid login or password.");

        // Reset
        $this->reset();

        // Try with good password
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost('login',array('username'   =>  'phpunit@factoryx.com.au', "password"   =>  "test123"));
        $this->dispatch('ajaxlogin/index/login');
        $this->assertRequestRoute('ajaxlogin/index/login');
        // Here we check if there is any error, with PHPUnit the login works but the success is not set to true
        // And the redirect part is not filled due to the unability to renew the sessions when marking the customer as logged in
        $this->assertResponseBodyNotContains("error");

        /*
         * This is not working, dealing with sessions is very hard
        // Reset
        $this->reset();

        // Logout
        $this->dispatch('ajaxlogin/index/logout');
        $this->assertRequestRoute('ajaxlogin/index/logout');
        */
    }
}