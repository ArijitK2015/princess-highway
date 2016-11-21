<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Instance of tested object
     * @return FactoryX_Contests_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('contests');
    }

    /**
     * @test
     * @loadExpectations
     * @dataProvider dataProvider
     * @param $n
     */
    public function numberSuffix($n)
    {
        $expected = $this->expected("1-$n")->getSuffix();
        $result = $this->_helper()->numberSuffix($n);
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectations
     * @dataProvider dataProvider
     * @param $n
     * @param $badlyFormattedDate
     */
    public function getCountdownFormattedEndDate($n, $badlyFormattedDate)
    {
        $expected = $this->expected("1-$n")->getDate();
        $result = $this->_helper()->getCountdownFormattedEndDate($badlyFormattedDate);
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectations
     */
    public function validateWordCount()
    {
        $expected = $this->expected("1-1")->getValidation();
        $result = $this->_helper()->validateWordCount("This is a test","Test",1,20);
        $this->assertEquals($expected, $result);

        $expected = $this->expected("1-2")->getValidation();
        $result = $this->_helper()->validateWordCount("This is a test","Test",10,20);
        $this->assertEquals($expected, $result);

        $expected = $this->expected("1-3")->getValidation();
        $result = $this->_helper()->validateWordCount("This is a test","Test",1,3);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getNotFoundRedirectUrl()
    {
        // Test without value set in the configuration
        $expected = Mage::helper('core/url')->getHomeUrl();
        $result = $this->_helper()->getNotFoundRedirectUrl();
        $this->assertEquals($expected,$result);

        // Test with value set in the configuration
        Mage::app()->getStore(0)->setConfig('contests/options/notfoundredirecturl','/phpunittest/');
        $expected = $this->expected("1-1")->getUrl();
        $result = $this->_helper()->getNotFoundRedirectUrl();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getTemplate()
    {
        Mage::app()->getStore(0)->setConfig('contests/options/template','php unit template');
        $expected = $this->expected("1-1")->getTemplate();
        $result = $this->_helper()->getTemplate();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getSender()
    {
        Mage::app()->getStore(0)->setConfig('contests/options/email','phpunit@factoryx.com.au');
        Mage::app()->getStore(0)->setConfig('contests/options/name','PHP Unit');
        $expected = $this->expected("1-1")->getSender();
        $result = $this->_helper()->getSender();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getStates()
    {
        $expected = $this->expected("1-1")->getStates();
        $result = $this->_helper()->getStates();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getAppId()
    {
        Mage::app()->getStore(0)->setConfig('contests/facebook/appId','php unit app id');
        $expected = $this->expected("1-1")->getId();
        $result = $this->_helper()->getAppId();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getAppSecret()
    {
        Mage::app()->getStore(0)->setConfig('contests/facebook/appSecret','php unit app secret');
        $expected = $this->expected("1-1")->getId();
        $result = $this->_helper()->getAppSecret();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function subscribeToCampaignMonitor()
    {
        $fields = array();
        $fields['name'] = "PHP Unit Test";
        $fields['email'] = "phpunittest@factoryx.com.au";
        $result = $this->_helper()->subscribeToCampaignMonitor($fields);
        $expected = $this->expected("1-1")->getSuccess();
        $this->assertEquals($expected,$result);

        $fields = array();
        $result = $this->_helper()->subscribeToCampaignMonitor($fields);
        $expected = $this->expected("1-2")->getSuccess();
        $this->assertEquals($expected,$result);
    }
}