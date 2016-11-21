<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_AdvertPanels_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Instance of tested object
     * @return FactoryX_Abandonedcarts_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('advertpanels');
    }

    /**
     * @test
     */
    public function isValidURL()
    {
        $result = $this->_helper()->isValidUrl("http://phpunit.factoryx.com.au");
        $expected = true;
        $this->assertEquals($expected,$result);

        $result = $this->_helper()->isValidUrl("phpunit.factoryx.com.au");
        $expected = false;
        $this->assertEquals($expected,$result);
    }
}