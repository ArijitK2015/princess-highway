<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Block_Contest_Popup extends EcomDev_PHPUnit_Test_Case
{
    public $_layout;
    public $_block;

    public function setUp()
    {
        /* You'll have to load Magento app in any test classes in this method */
        $app = Mage::app();
        /* You will need a layout for block tests */
        $this->_layout = $app->getLayout();
        /* Let's create the block instance for further tests */
        $this->_block = new FactoryX_Contests_Block_Contest_Popup;
        /* We are required to set layouts before we can do anything with blocks */
        $this->_block->setLayout($this->_layout);
    }

    /**
     * @test
     * @loadFixture popup.yaml
     */
    public function hasPopupContest()
    {
        $this->assertEquals(true,$this->_block->hasPopupContest());

        @session_destroy();
        @session_start();

        $contest = Mage::getModel('contests/contest')->load(2);
        $contest->setIsPopup(1);
        $contest->save();

        $this->assertEquals(false,$this->_block->hasPopupContest());

        @session_destroy();
        @session_start();

        $contest = Mage::getModel('contests/contest')->load(1);
        $contest->setIsPopup(0);
        $contest->save();

        $contest = Mage::getModel('contests/contest')->load(2);
        $contest->setIsPopup(0);
        $contest->save();

        $this->assertEquals(false,$this->_block->hasPopupContest());

        @session_destroy();
    }

    /**
     * @test
     * @loadFixture popup.yaml
     */
    public function getReferersLimitation()
    {
        if ($this->_block->hasPopupContest())
        {
            $this->assertEquals(array("www.facebook.com"),$this->_block->getReferersLimitation());
        }
    }

    /**
     * @test
     * @loadFixture popup.yaml
     */
    public function getPopupText()
    {
        if ($this->_block->hasPopupContest())
        {
            $this->assertEquals("Popup Test",$this->_block->getPopupText());
        }
    }

    /**
     * @test
     * @loadFixture popup.yaml
     */
    public function getPopupIdentifier()
    {
        if ($this->_block->hasPopupContest())
        {
            $this->assertEquals("phpunittest",$this->_block->getPopupIdentifier());
        }
    }

    /**
     * @test
     * @loadFixture popup.yaml
     */
    public function getPopupContestId()
    {
        if ($this->_block->hasPopupContest())
        {
            $this->assertEquals(1,$this->_block->getPopupContestId());
        }
    }
}