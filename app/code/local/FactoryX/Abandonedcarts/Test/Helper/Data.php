<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Abandonedcarts_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Instance of tested object
     * @return FactoryX_Abandonedcarts_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('abandonedcarts');
    }

    /**
     * @test
     */
    public function isEnabledWithoutConfig()
    {
        // Test without value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->isEnabled();
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     * @loadFixture
     */
    public function isEnabledWithConfig()
    {
        // Test with value set in the configuration
        $result = $this->_helper()->isEnabled();
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function isSaleEnabledWithoutConfig()
    {
        // Test without value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->isSaleEnabled();
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     * @loadFixture
     */
    public function isSaleEnabledWithConfig()
    {
        // Test with value set in the configuration
        $result = $this->_helper()->isSaleEnabled();
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     */
    public function getDryRunWithoutConfig()
    {
        // Test without value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->getDryRun();
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     * @loadFixture
     */
    public function getDryRunWithConfig()
    {
        // Test with value set in the configuration
        $result = $this->_helper()->getDryRun();
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     */
    public function getTestEmailWithoutConfig()
    {
        // Test without value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->getTestEmail();
        $this->assertEquals("", $result);
    }

    /**
     * @test
     * @loadFixture
     */
    public function getTestEmailWithConfig()
    {
        // Test with value set in the configuration
        $result = $this->_helper()->getTestEmail();
        $this->assertEquals('phpunit@factoryx.com.au',$result);
    }
}