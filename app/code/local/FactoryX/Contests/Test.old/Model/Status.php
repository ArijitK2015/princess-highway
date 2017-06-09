<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Status extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadExpectation
     */
    public function getEnabledStatusIds()
    {
        $expected = array($this->expected("1-1")->getStatus());
        $result = Mage::getModel("contests/status")->getEnabledStatusIds();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getDisabledStatusIds()
    {
        $expected = array($this->expected("1-1")->getStatus());
        $result = Mage::getModel("contests/status")->getDisabledStatusIds();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getAutomaticStatusIds()
    {
        $expected = array($this->expected("1-1")->getStatus());
        $result = Mage::getModel("contests/status")->getAutomaticStatusIds();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @loadExpectation
     */
    public function getOptionArray()
    {
        $expected = array(
                $this->expected("1-1")->getStatus() =>  $this->expected("1-1")->getLabel(),
                $this->expected("1-2")->getStatus() =>  $this->expected("1-2")->getLabel(),
                $this->expected("1-3")->getStatus() =>  $this->expected("1-3")->getLabel()
                );
        $result = Mage::getModel("contests/status")->getOptionArray();
        $this->assertEquals($expected, $result);
    }
}