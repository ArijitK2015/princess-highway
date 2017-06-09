<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Core_Url_Rewrite extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture
     */
    public function loadByRequestPathAndStoreId()
    {
        $loaded = Mage::getModel('core/url_rewrite')->loadByRequestPathAndStoreId('phpunit',1);
        $this->assertEquals('contests/view/id/1',$loaded->getTargetPath());
    }
}