<?php

/**
 * Class FactoryX_ShippedFrom_Adminhtml_ShippedfromlogsController
 */
class FactoryX_ShippedFrom_Adminhtml_ShippedfromlogsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/auspost/logs');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('factoryx_menu/auspost/logs');

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('shippedfrom/adminhtml_log'));
        $this->renderLayout();
    }
}