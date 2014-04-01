<?php

class FactoryX_Homepage_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
     * Preview action
     */
    public function previewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}