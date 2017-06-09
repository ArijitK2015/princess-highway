<?php
/**

*/
class FactoryX_PickList_Block_Adminhtml_Output_Error extends Mage_Core_Block_Template {

    /**
     * block constructor
     */
    public function __construct() {
        parent::__construct();

        $this->_blockGroup = 'picklist';
        $this->_controller = 'adminhtml_picklist';
    }

}
