<?php

class Temando_Temando_Model_System_Config_Source_Pricing extends Temando_Temando_Model_System_Config_Source
{
    
    const FREE                         = 'free';
    const FLAT_RATE                    = 'flat';
    const DYNAMIC                      = 'dynamic';
    const DYNAMIC_FASTEST              = 'dynamicfast';
    const DYNAMIC_CHEAPEST             = 'dynamiccheap';
    const DYNAMIC_FASTEST_AND_CHEAPEST = 'dynamicfastcheap';
    
    protected function _setupOptions()
    {
        $this->_options = array(
            self::FREE                         => 'Free Shipping',
            self::FLAT_RATE                    => 'Fixed Price / Flat Rate',
            self::DYNAMIC                      => 'Dynamic Pricing (All)',
            self::DYNAMIC_CHEAPEST             => 'Dynamic Pricing (Cheapest only)',
            self::DYNAMIC_FASTEST              => 'Dynamic Pricing (Fastest only)',
            self::DYNAMIC_FASTEST_AND_CHEAPEST => 'Dynamic Pricing (Cheapest and Fastest only)',
        );
    }
    
}
