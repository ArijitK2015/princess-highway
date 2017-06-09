<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_CacheSupport_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Instance of tested object
     * @return FactoryX_Abandonedcarts_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('factoryx_cachesupport');
    }

    /**
     * @test
     * @loadFixture
     */
    public function getRecentlyViewedEnableWithConfig()
    {
        // Test with value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->getRecentlyViewedEnable();
        $this->assertEquals(true, (boolean)$result);
    }

    /**
     * @test
     */
    public function getRecentlyViewedEnableWithoutConfig()
    {
        // Test without value set in the configuration
        $result = $this->_helper()->getRecentlyViewedEnable();
        $this->assertEquals(false,(boolean)$result);
    }

    /**
     * @test
     * @loadFixture
     */
    public function getRecentlyViewedTtlWithConfig()
    {
        // Test with value set in the configuration
        $result = $this->_helper()->getRecentlyViewedTtl();
        $this->assertEquals(100, $result);
    }

    /**
     * @test
     */
    public function getRecentlyViewedTtlWithoutConfig()
    {
        // Test without value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->getRecentlyViewedTtl();
        $this->assertEquals(1,$result);
    }

    /**
     * @test
     * @loadFixture
     */
    public function getRecentlyViewedCountWithConfig()
    {
        // Test with value set in the configuration
        $result = $this->_helper()->getRecentlyViewedCount();
        $this->assertEquals(10, $result);
    }

    /**
     * @test
     */
    public function getRecentlyViewedCountWithoutConfig()
    {
        // Test without value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->getRecentlyViewedCount();
        $this->assertEquals(5,$result);
    }

    /**
     * @test
     * @loadFixture
     */
    public function getTopLinksEnableWithConfig()
    {
        // Test with value set in the configuration
        $result = $this->_helper()->getTopLinksEnable();
        $this->assertEquals(false, (boolean)$result);
    }

    /**
     * @test
     */
    public function getTopLinksEnableWithoutConfig()
    {
        // Test with value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->getTopLinksEnable();
        $this->assertEquals(true,(boolean)$result);
    }

    /**
     * @test
     * @loadFixture
     */
    public function getMobileCartEnableWithConfig()
    {
        // Test with value set in the configuration
        $result = $this->_helper()->getMobileCartEnable();
        $this->assertEquals(false, (boolean)$result);
    }

    /**
     * @test
     */
    public function getMobileCartEnableWithoutConfig()
    {
        // Test without value set in the configuration
        // Should take the default values from config.xml
        $result = $this->_helper()->getMobileCartEnable();
        $this->assertEquals(true,(boolean)$result);
    }
}