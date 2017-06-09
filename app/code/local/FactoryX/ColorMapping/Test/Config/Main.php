<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ColorMapping_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.1.6");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('colormapping','FactoryX_ColorMapping_Helper_Data');

        // Blocks rewrites
        $this->assertBlockAlias('colormapping/adminhtml_system_config_form_field_mappingcolors','FactoryX_ColorMapping_Block_Adminhtml_System_Config_Form_Field_Mappingcolors');
    }
}