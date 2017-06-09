<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why:  
 */
class FactoryX_NotificationBoard_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     *
     */
    public function __construct()
    {
        $this->_blockGroup      = 'factoryx_notificationboard';
        $this->_controller      = 'adminhtml_notification';
        $this->_headerText      = $this->__('Factory X Notification');
        // $this->_addButtonLabel  = $this->__('Add Button Label');
        parent::__construct();
            }

    /**
     * @return mixed
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

}

