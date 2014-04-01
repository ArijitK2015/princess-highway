<?php

class FactoryX_Homepage_Block_Homepage_Blocks_Slider extends Mage_Core_Block_Template 
{
	/**
	 *	Constructor for a frontend homepage slider
	 */
	public function __construct()
    {
        parent::__construct();
		
		// Set the right template
		$this->setTemplate('factoryx/homepage/blocks/slider.phtml');
    }
}
?>