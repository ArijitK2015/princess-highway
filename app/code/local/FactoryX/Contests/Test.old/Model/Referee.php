<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Referee extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture
     * @loadExpectation
     */
    public function loadByEmailAndContest()
    {
        $expected = $this->expected("1-1")->getId();
        $result = Mage::getModel("contests/referee")->loadByEmailAndContest("unittest@factoryx.com.au",1)->getRefereeId();
        $this->assertEquals($expected, $result);
    }
}