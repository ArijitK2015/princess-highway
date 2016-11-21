<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Referrer extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture referrer.yaml
     * @loadExpectation
     */
    public function loadByEmailAndContest()
    {
        $expected = $this->expected("1-1")->getId();
        $result = Mage::getModel("contests/referrer")->loadByEmailAndContest("unittest@factoryx.com.au",1)->getReferrerId();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @loadFixture referrer.yaml
     * @loadExpectation
     */
    public function wins()
    {
        $expected = $this->expected("1-1")->getWins();
        $referrer = Mage::getModel("contests/referrer")->load(1);
        $referrer->wins();
        $result = $referrer->getIsWinner();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @loadFixture referrer.yaml
     * @loadExpectation
     */
    public function reset()
    {
        $expected = $this->expected("1-1")->getWins();
        $referrer = Mage::getModel("contests/referrer")->load(1);
        $referrer->wins();
        $referrer->reset();
        $result = $referrer->getIsWinner();
        $this->assertEquals($expected, $result);
    }
}