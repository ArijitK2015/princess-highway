<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Contest extends EcomDev_PHPUnit_Test_Case
{
    public function setUp()
    {
        @session_start();
        parent::setUp();
    }

    /**
     * @test
     * @loadFixture contest.yaml
     * @loadExpectation
     */
    public function getUrlInStore()
    {
        $expected = $this->expected("1-1")->getUrl();
        $result = Mage::getModel("contests/contest")->load(1)->getUrlInStore();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     * @loadExpectation
     */
    public function isStoreViewable()
    {
        $this->setCurrentStore('default');
        $expected = $this->expected("1-1")->getViewable();
        $result = Mage::getModel("contests/contest")->load(1)->isStoreViewable();
        $this->assertTrue($expected == $result);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     * @loadExpectation
     */
    public function winnersCount()
    {
        $expected = $this->expected("1-1")->getCount();
        $result = Mage::getModel("contests/contest")->load(1)->winnersCount();
        $this->assertEquals($expected, $result);

        $expected = $this->expected("1-2")->getCount();
        $result = Mage::getModel("contests/contest")->load(2)->winnersCount();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     * @loadExpectation
     */
    public function isAllowedDuplicateEntries()
    {
        $expected = $this->expected("1-1")->getFlag();
        $result = Mage::getModel("contests/contest")->load(1)->isAllowedDuplicateEntries();
        $this->assertTrue($expected == $result);

        $expected = $this->expected("1-2")->getFlag();
        $result = Mage::getModel("contests/contest")->load(2)->isAllowedDuplicateEntries();
        $this->assertTrue($expected == $result);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     * @loadExpectation
     */
    public function isAllowedDuplicateReferrals()
    {
        $expected = $this->expected("1-1")->getFlag();
        $result = Mage::getModel("contests/contest")->load(1)->isAllowedDuplicateReferrals();
        $this->assertTrue($expected == $result);

        $expected = $this->expected("1-2")->getFlag();
        $result = Mage::getModel("contests/contest")->load(2)->isAllowedDuplicateReferrals();
        $this->assertTrue($expected == $result);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     * @loadExpectation
     */
    public function isAutomatic()
    {
        $expected = $this->expected("1-1")->getFlag();
        $result = Mage::getModel("contests/contest")->load(1)->isAutomatic();
        $this->assertTrue($expected == $result);

        $expected = $this->expected("1-2")->getFlag();
        $result = Mage::getModel("contests/contest")->load(2)->isAutomatic();
        $this->assertTrue($expected == $result);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     * @loadExpectation
     */
    public function isGiveAwayContest()
    {
        $expected = $this->expected("1-1")->getFlag();
        $result = Mage::getModel("contests/contest")->load(1)->isGiveAwayContest();
        $this->assertTrue($expected == $result);

        $expected = $this->expected("1-2")->getFlag();
        $result = Mage::getModel("contests/contest")->load(2)->isGiveAwayContest();
        $this->assertTrue($expected == $result);
    }

    /**
     * @test
     * @loadFixture contest.yaml
     * @loadExpectation
     */
    public function isReferAFriendContest()
    {
        $expected = $this->expected("1-1")->getFlag();
        $result = Mage::getModel("contests/contest")->load(1)->isReferAFriendContest();
        $this->assertTrue($expected == $result);

        $expected = $this->expected("1-2")->getFlag();
        $result = Mage::getModel("contests/contest")->load(2)->isReferAFriendContest();
        $this->assertTrue($expected == $result);
    }
}