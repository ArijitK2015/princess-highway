<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_BirthdayGift_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.6.3");
        $this->assertModuleDepends('FactoryX_CampaignMonitor');
        $this->assertModuleDepends('FactoryX_PromoRestriction');
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('birthdaygift','FactoryX_BirthdayGift_Helper_Data');

        // Models
        $this->assertModelAlias('birthdaygift/observer','FactoryX_BirthdayGift_Model_Observer');
    }
}
