<?php

/**
 * Retail Evolved - Facebook Like Analytics
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with this
 * package in the file EVOLVED_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://retailevolved.com/eula-1-0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to service@retailevolved.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * You may edit this file, but only at your own risk, as long as it is within
 * the constraints of the license agreement. Before upgrading the module (not Magento), 
 * be sure to back up your original installation as the upgrade may override your
 * changes.
 *
 * @category   Evolved
 * @package    Evolved_LikeAnalytics
 * @copyright  Copyright (c) 2010 Kaelex Inc. DBA Retail Evolved (http://retailevolved.com)
 * @license    http://retailevolved.com/eula-1-0 (Retail Evolved EULA 1.0)
 */

class Evolved_LikeAnalytics_Adminhtml_GenerateController extends Mage_Adminhtml_Controller_Action
{
	/**
     * Retrieve session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
	
	public function indexAction() {
		$this->loadLayout()
            ->_setActiveMenu('evlikeanalytics/generate')
            ->_addContent($this->getLayout()->createBlock('evlikeanalytics/adminhtml_generate'))
            ->renderLayout();
	}
	
	public function refreshAction() {
		try {
            $flag = Mage::helper('evlikeanalytics')->getFlag();
            if ($flag->getState() == Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING) {
                $kill = Mage::helper('evlikeanalytics')->getKillFlag();
                $kill->setFlagData($flag->getFlagData())->save();
            }

            $flag->setState(Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED)->save();
            Mage::getSingleton('evlikeanalytics/stat')->generate();

            $this->_getSession()->addSuccess(
                Mage::helper('evlikeanalytics')->__('Stats were refreshed successfully')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
						$this->_getSession()->addError($e->getMessage());
            //$this->_getSession()->addException($e, Mage::helper('evlikeanalytics')->__('Error while refreshing stats. Please try again later'));
        }

		$this->_redirect('*/*');
	}
}