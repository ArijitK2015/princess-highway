<?php

class Temando_Temando_Model_System_Config_Source_Footprints extends Temando_Temando_Model_System_Config_Source
{
    
    const DISABLED  = 'disabled';
    const OPTIONAL  = 'optional';
    const MANDATORY = 'mandatory';
    
    protected function _setupOptions()
    {
        $this->_options = array(
            self::DISABLED  => 'Disabled',
            self::OPTIONAL  => 'Optional',
            self::MANDATORY => 'Mandatory',
        );
    }
    
}
