<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 10/12/2014
 * Time: 17:04
 */

class FactoryX_Abandonedcarts_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @test
     * @loadExpectation
     */
    public function generateRecipients()
    {
        $array = array('row'    =>  array(
                                        'customer_email'    =>  "phpunit@factoryx.com.au",
                                        'customer_firstname'=>  "PHP Unit",
                                        'customer_lastname' =>  "Test",
                                        'product_name'      =>  "Product Test",
                                        'cart_id'           =>  999999));
        $observer = Mage::getModel('abandonedcarts/notifier');
        $observer->generateRecipients($array);
        $result = $observer->getRecipients();
        $expected = $this->expected("1-1")->getRecipients();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function generateSaleRecipients()
    {
        $this->markTestIncomplete("Buggy with sessions");
        $array = array('row'    =>  array(
            'customer_email'    =>  "phpunit@factoryx.com.au",
            'customer_firstname'=>  "PHP Unit",
            'customer_lastname' =>  "Test",
            'product_name'      =>  "Product Test",
            'product_price_in_cart' => 100,
            'product_special_price' => 75,
            'cart_id'           =>  999999));
        $observer = Mage::getModel('abandonedcarts/notifier');
        $observer->generateSaleRecipients($array);
        $result = $observer->getSaleRecipients();
        $expected = $this->expected("1-1")->getRecipients();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     */
    public function getRecipients()
    {
        $observer = Mage::getModel('abandonedcarts/notifier');
        $result = $observer->getRecipients();
        $expected = array();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     */
    public function getSaleRecipients()
    {
        $observer = Mage::getModel('abandonedcarts/notifier');
        $result = $observer->getSaleRecipients();
        $expected = array();
        $this->assertEquals($expected,$result);
    }

    /**
     * @test
     * @todo
     */
    public function sendAbandonedCartsEmail()
    {

    }

    /**
     * @test
     * @todo
     */
    public function sendAbandonedCartsSaleEmail()
    {

    }

    /**
     * @test
     * @todo
     */
    public function sendEmails()
    {

    }

    /**
     * @test
     * @todo
     */
    public function sendSaleEmails()
    {

    }

}