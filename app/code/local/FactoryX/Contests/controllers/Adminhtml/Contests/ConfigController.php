<?php

/**
 * Class FactoryX_Contests_Adminhtml_ContestsController
 */
class FactoryX_Contests_Adminhtml_Contests_ConfigController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/contests');
    }

    public function triggerAction()
    {
        $model = Mage::getModel('contests/observer');
        $model->toggleContests();

        $result = 1;
        Mage::app()->getResponse()->setBody($result);
    }

}