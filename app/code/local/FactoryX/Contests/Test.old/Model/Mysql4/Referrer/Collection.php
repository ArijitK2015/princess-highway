<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Mysql4_Referrer_Collection extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture referrer.yaml
     */
    public function addContestFilter()
    {
        $collection = Mage::getResourceModel('contests/referrer_collection')->addContestFilter(1);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/referrer_collection')->addContestFilter(3);
        $this->assertEquals($collection->getSize(),0);
    }

    /**
     * @test
     * @loadFixture referrer.yaml
     */
    public function addStateFilter()
    {
        $collection = Mage::getResourceModel('contests/referrer_collection')->addStateFilter("VIC");
        $this->assertEquals($collection->getSize(),2);

        $collection = Mage::getResourceModel('contests/referrer_collection')->addStateFilter("NZ");
        $this->assertEquals($collection->getSize(),0);
    }

    /**
     * @test
     * @loadFixture referrer.yaml
     */
    public function addWinnersFilter()
    {
        $collection = Mage::getResourceModel('contests/referrer_collection')->addWinnersFilter();
        $this->assertEquals($collection->getSize(),1);
    }

    /**
     * @test
     * @loadFixture referrer.yaml
     */
    public function addContestData()
    {
        $collection = Mage::getResourceModel('contests/referrer_collection')->addContestData();
        $this->assertEquals($collection->getFirstItem()->getContestTitle(),"Bla Bla Bla");
    }

    /**
     * @test
     * @loadFixture referrer.yaml
     */
    public function addRefereeData()
    {
        $collection = Mage::getResourceModel('contests/referrer_collection')->addRefereeData();
        $this->assertEquals($collection->getFirstItem()->getEmail(),"phpunit@factoryx.com.au");
    }
}