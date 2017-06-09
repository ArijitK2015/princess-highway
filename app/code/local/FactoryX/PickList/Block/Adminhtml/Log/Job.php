<?php
/**
 */

class FactoryX_PickList_Block_Adminhtml_Log_Job extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
    /**
     * Block constructor
     */
    public function __construct() {
        $this->_blockGroup = 'picklist';
        $this->_controller = 'adminhtml_log_job';
        $this->_headerText = Mage::helper('cms')->__('Pick List - Job Log');
        parent::__construct();
        
        // Remove the add button
        $this->_removeButton('add');
    }

}
