<?php

/**
 * Class FactoryX_ReportsViewer_Adminhtml_ReportsviewerController
 */
class FactoryX_ReportsViewer_Adminhtml_ReportsviewerController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Check the ACL permission
	 * @return mixed
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('system/tools/reportsviewer');
	}

	/**
	 * This is the action used to display the grid
	 */
	public function indexAction()
	{
		$this->_title($this->__('System'))->_title($this->__('Tools'))->_title($this->__('Reports Viewer'));

		if($this->getRequest()->getParam('ajax')) {
			$this->_forward('grid');
			return;
		}

		$this->loadLayout();
		$this->_setActiveMenu('system');
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Reports Viewer'), Mage::helper('adminhtml')->__('Reports Viewer'));

		$this->_addContent($this->getLayout()->createBlock('reportsviewer/adminhtml_reportsviewer', 'reportsviewer'));

		$this->renderLayout();
	}

	/**
	 * Report list action
	 */
	public function gridAction()
	{
		$this->getResponse()->setBody($this->getLayout()->createBlock('reportsviewer/adminhtml_reportsviewer_grid')->toHtml());
	}

	/**
	 * This is called when deleting an item from its edit page
	 */
	public function deleteAction() {
		// We first retrieve the ID
		$reportId = (int) $this->getRequest()->getParam('id');
		// Set the location of the report
		$reportFolder = Mage::getBaseDir('var') . '/report';

		if ($reportId)
		{
			try {
				// We physically (so to say) delete the file
				unlink($reportFolder . "/" . $reportId);
				// Success message
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('reportsviewer')->__('Report was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		// Redirection to the grid
		$this->_redirect('*/*/');
	}

	/**
	 * This is called when we mass delete items from the grid
	 */
	public function massDeleteAction()
	{
		// We get the IDs of the items that need to be deleted
		$reportIds = $this->getRequest()->getParam('ids');
		// Set the location of the reports
		$reportFolder = Mage::getBaseDir('var') . '/report';

		if (!is_array($reportIds))
		{
			// Display an error if the parameter is not an array
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reportsviewer')->__('Please select report(s)'));
		}
		else
		{
			try {
				// Loop through the reports IDs
				foreach($reportIds as $reportId)
				{
					// Delete them manually
					unlink($reportFolder . "/" . $reportId);
				}
				// Success message
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('reportsviewer')->__(
						'Total of %d report(s) were successfully deleted', count($reportIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		// Redirection to the grid
		$this->_redirect('*/*/index');
	}

	/**
	 * This is the action used to view the details of a report
	 */
	public function editAction()
	{
		// We first retrieve the report ID
		$id = $this->getRequest()->getParam('id');
		// Then we generate the report path
		$path = Mage::getBaseDir('var') . DS . 'report';

		// Load the report
		$model = Mage::getModel('reportsviewer/report')->load($id, $path);

		// Register the data so we can use it in the form
		Mage::register('report_data', $model);

		// Layout loading / rendering
		$this->loadLayout();
		$this->_setActiveMenu('system/tools/reportsviewer');

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('reportsviewer/adminhtml_reportsviewer_edit'));

		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);


		$this->renderLayout();
	}

}