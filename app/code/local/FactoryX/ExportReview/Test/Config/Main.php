<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ExportReview_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.1.6");
    }

    public function testClassAliasDefinitions()
    {
        // Model
        $this->assertModelAlias('exportreview/observer','FactoryX_ExportReview_Model_Observer');
    }
}