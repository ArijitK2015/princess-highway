<?php

/**
 * Class FactoryX_Homepage_Block_Homepage_Blocks_Cell
 */
class FactoryX_Homepage_Block_Homepage_Blocks_Cell extends Mage_Core_Block_Template
{
	/**
	 *	Constructor for a frontend homepage cell
	 */
	public function __construct()
    {
        parent::__construct();
		
		// Set the right template
		$this->setTemplate('factoryx/homepage/blocks/cell.phtml');
    }
}