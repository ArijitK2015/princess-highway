<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Mysql4_Contest_Collection extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture contest.yaml
     */
    public function addIsPopupFilter()
    {
        $collection = Mage::getResourceModel('contests/contest_collection')->addIsPopupFilter(1);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/contest_collection')->addIsPopupFilter(0);
        $this->assertEquals($collection->getSize(),1);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     */
    public function addNotIdsFilter()
    {
        $collection = Mage::getResourceModel('contests/contest_collection')->addNotIdsFilter(1);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/contest_collection')->addNotIdsFilter(array(1,2));
        $this->assertEquals($collection->getSize(),0);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     */
    public function addIdFilter()
    {
        $collection = Mage::getResourceModel('contests/contest_collection')->addIdFilter(1);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/contest_collection')->addIdFilter(array(1,2));
        $this->assertEquals($collection->getSize(),2);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     */
    public function addInListFilter()
    {
        $collection = Mage::getResourceModel('contests/contest_collection')->addInListFilter(1);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/contest_collection')->addInListFilter(0);
        $this->assertEquals($collection->getSize(),1);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     */
    public function addDisplayedFilter()
    {
        $collection = Mage::getResourceModel('contests/contest_collection')->addDisplayedFilter(1);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/contest_collection')->addDisplayedFilter(0);
        $this->assertEquals($collection->getSize(),1);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     */
    public function addStatusFilter()
    {
        $collection = Mage::getResourceModel('contests/contest_collection')->addStatusFilter(1);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/contest_collection')->addStatusFilter(0);
        $this->assertEquals($collection->getSize(),1);

        $collection = Mage::getResourceModel('contests/contest_collection')->addStatusFilter(2);
        $this->assertEquals($collection->getSize(),0);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     */
    public function addStoreFilter()
    {
        $collection = Mage::getResourceModel('contests/contest_collection')->addStoreFilter(1);
        if (!Mage::app()->isSingleStoreMode())
        {
            $this->assertEquals($collection->getSize(),1);
        }
        else $this->assertEquals($collection->getSize(),2);

        $collection = Mage::getResourceModel('contests/contest_collection')->addStoreFilter(2);
        if (!Mage::app()->isSingleStoreMode())
        {
            $this->assertEquals($collection->getSize(),1);
        }
        else $this->assertEquals($collection->getSize(),2);

        $collection = Mage::getResourceModel('contests/contest_collection')->addStoreFilter(3);
        if (!Mage::app()->isSingleStoreMode())
        {
            $this->assertEquals($collection->getSize(),0);
        }
        else $this->assertEquals($collection->getSize(),2);
    }
}