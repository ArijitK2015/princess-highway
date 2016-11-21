<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Block_Contest_Terms extends EcomDev_PHPUnit_Test_Case
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
        $this->_block = new FactoryX_Contests_Block_Contest_Terms;
        /* We are required to set layouts before we can do anything with blocks */
        $this->_block->setLayout($this->_layout);
        /* Assign the ID */
        $this->_block->getRequest()->setParam('id',1);
    }

    /**
     * @test
     * @loadFixture terms.yaml
     */
    public function getCurrentContestTitle()
    {
        $this->assertEquals("PHPUnit Test",$this->_block->getCurrentContestTitle());
    }

    /**
     * @test
     * @loadFixture terms.yaml
     */
    public function getCurrentContestTerms()
    {
        $this->assertEquals("Bla Bla Bla",$this->_block->getCurrentContestTerms());
    }
}