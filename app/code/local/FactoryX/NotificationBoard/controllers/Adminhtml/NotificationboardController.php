<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why:
 */

class FactoryX_NotificationBoard_Adminhtml_NotificationboardController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/factoryx_notification');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('factoryx_notificationboard/adminhtml_notification'));
        $this->renderLayout();
    }

    public function exportCsvAction()
    {
        $fileName = 'Notification_export.csv';
        $content = $this->getLayout()->createBlock('factoryx_notificationboard/adminhtml_notification_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportExcelAction()
    {
        $fileName = 'Notification_export.xml';
        $content = $this->getLayout()->createBlock('factoryx_notificationboard/adminhtml_notification_grid')->getExcel();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select Notification(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('factoryx_notificationboard/notification')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('factoryx_notificationboard')->__('An error occurred while mass deleting items. Please review log and try again.')
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('factoryx_notificationboard/notification');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('factoryx_notificationboard')->__('This Notification no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('current_model', $model);

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('factoryx_notificationboard/adminhtml_notification_edit'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('factoryx_notificationboard/notification');
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->_getSession()->addError(
                        Mage::helper('factoryx_notificationboard')->__('This Notification no longer exists.')
                    );
                    $this->_redirect('*/*/index');
                    return;
                }
            }

            // save model
            try {
                $model->addData($data);

                if (isset($data['start_date']) && $data['start_date'])
                {
                    // Convert the date properly
                    $startdate = Mage::app()->getLocale()->date($data['start_date'], Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
                    // Set the times to zero
                    $startdate->set('00:00:00',Zend_Date::TIMES);
                }

                if (isset($data['end_date']) && $data['end_date'])
                {
                    // Convert the date properly
                    $enddate = Mage::app()->getLocale()->date($data['end_date'], Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
                    // Set the times to zero
                    $enddate->set('23:59:59',Zend_Date::TIMES);
                }

                // End date must be later than start date
                if (isset($enddate) && isset($startdate) && $startdate->isLater($enddate))
                {
                    throw new Exception ("Start date must not be later than end date.");
                }

                if (isset($startdate))  $model->setStartDate($startdate);
                if (isset($enddate))  $model->setEndDate($enddate);
                $this->_getSession()->setFormData($data);
                $model->save();
                $this->_getSession()->setFormData(false);
                $this->_getSession()->addSuccess(
                    Mage::helper('factoryx_notificationboard')->__('The Notification has been saved.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('factoryx_notificationboard')->__('Unable to save the Notification: %s',$e->getMessage()));
                $redirectBack = true;
                Mage::logException($e);
            }

            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('factoryx_notificationboard/notification');
                $model->load($id);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('factoryx_notificationboard')->__('Unable to find a Notification to delete.'));
                }
                $model->delete();
                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('factoryx_notificationboard')->__('The Notification has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/index');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('factoryx_notificationboard')->__('An error occurred while deleting Notification data. Please review log and try again.')
                );
                Mage::logException($e);
            }
            // redirect to edit form
            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }
// display error message
        $this->_getSession()->addError(
            Mage::helper('factoryx_notificationboard')->__('Unable to find a Notification to delete.')
        );
// go to grid
        $this->_redirect('*/*/index');
    }
}