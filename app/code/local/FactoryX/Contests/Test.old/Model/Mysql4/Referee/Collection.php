<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Mysql4_Referee_Collection extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture referee.yaml
     */
    public function addContestFilter()
    {
        $collection = Mage::getResourceModel('contests/referee_collection')->addContestFilter(1);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/referee_collection')->addContestFilter(2);
        $this->assertEquals($collection->getSize(),0);
    }

    /**
     * @test
     * @loadFixture referee.yaml
     */
    public function addContestData()
    {
        $collection = Mage::getResourceModel('contests/referee_collection')->addContestData();
        $this->assertEquals($collection->getFirstItem()->getContestTitle(),"Bla Bla Bla");
    }

    /**
     * @test
     * @loadFixture referee.yaml
     */
    public function addReferrerData()
    {
        $collection = Mage::getResourceModel('contests/referee_collection')->addReferrerData();
        $this->assertEquals($collection->getFirstItem()->getReferrerEmail(),"phpunit2@factoryx.com.au");
    }

    /**
     * @test
     * @loadFixture referee.yaml
     */
    public function filterByReferrerEmail()
    {
        $collection = Mage::getResourceModel('contests/referee_collection')->filterByReferrerEmail("phpunit2@factoryx.com.au");
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/referee_collection')->filterByReferrerEmail("phpunit@factoryx.com.au");
        $this->assertEquals($collection->getSize(),0);
    }
}