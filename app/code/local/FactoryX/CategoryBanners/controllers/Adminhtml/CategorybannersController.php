<?php

/**
 * Class FactoryX_CategoryBanners_Adminhtml_CategorybannersController
 * This is the adminhtml controller
 */
class FactoryX_CategoryBanners_Adminhtml_CategorybannersController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Checks the ACL permissions
	 * @return mixed
     */
	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/categorybanners');
    }

	/**
	 * Initiate the action
	 * @return $this
	 */
	protected function _initAction()
	{
		$this->loadLayout()->_setActiveMenu('factoryx_menu/categorybanners');

		return $this;
	}

	/**
	 *	This is the grid page action
     */
	public function indexAction()
	{
		try {
			$this->_initAction();
			$this->_addContent($this->getLayout()->createBlock('categorybanners/adminhtml_categorybanners'));
			$this->renderLayout();
		}
		catch(Exception $ex) {
			Mage::helper('categorybanners')->log(sprintf("%s->error=%s", __METHOD__, print_r($ex, true)), Zend_Log::DEBUG );
		}
	}

	/**
	 *	This is the delete action from the edit page
     */
	public function deleteAction() {
		// Retrieve the ID of the banner
		$bannerId = (int) $this->getRequest()->getParam('id');
		if ($bannerId)
		{
			try {
				// Delete it
				$model = Mage::getModel('categorybanners/banner')->load($bannerId);
				$model->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('categorybanners')->__('Banner was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	/**
	 *	This is an internal redirect from new action to edit action
     */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 *	This is the action of the edit page
     */
	public function editAction()
	{
		// We first retrieve the ID of the banner
		$id = $this->getRequest()->getParam('id');
		// Then load it
		$model = Mage::getModel('categorybanners/banner')->load($id);

		// Check if the banner exists
		if ($model->getId() || $id == 0)
		{
			// Retrieve data from the form
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

			// If data from the form, we add it to the model loaded
			if (!empty($data)) {
				$model->setData($data);
			}

			// We register the model
			Mage::register('banner_data', $model);

			// Load and render the layout
			$this->loadLayout();
			$this->_setActiveMenu('factoryx_menu/categorybanners');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('categorybanners/adminhtml_categorybanners_edit'));

			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);


			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('categorybanners')->__('Banner does not exist'));
			$this->_redirect('*/*/');
		}
	}

	/**
	 *	This is the action called when clicking the save button
     */
	public function saveAction()
	{
		// We retrieve the POST data
		if ($data = $this->getRequest()->getPost())
		{
			// Get the model
			$model = Mage::getModel('categorybanners/banner');

			try
			{

				// Before checking for errors we need to process the image data so they won't be lost in case of errors
				// Save the frontend banner image
				if(isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name'])))
				{
					// Upload the image
					$uploader = Mage::getModel('core/file_uploader','image');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$result = $uploader->save(
						Mage::getSingleton('categorybanners/banner_media_config')->getBaseMediaPath()
					);

					// Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
					$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
					$result['path'] = str_replace(DS, "/", $result['path']);

					$result['url'] = Mage::getSingleton('categorybanners/banner_media_config')->getMediaUrl($result['file']);
					$result['cookie'] = array(
						'name'     => session_name(),
						'value'    => $this->_getSession()->getSessionId(),
						'lifetime' => $this->_getSession()->getCookieLifetime(),
						'path'     => $this->_getSession()->getCookiePath(),
						'domain'   => $this->_getSession()->getCookieDomain()
					);

					// This is the final data
					$data['image'] = $result['file'];
				}
				else
				{
					// Delete the image if the checkbox is ticked
					if(isset($data['image']['delete']) && $data['image']['delete'] == 1)
					{
						$data['image'] = NULL;
					}
				}

				// Fix if existing image and no reupload
				if (isset($data['image']))
				{
					if (is_array($data['image']))
					{
						$data['image'] = $data['image']['value'];
					}
				}

				// Edited date
				$data['edited'] = Mage::getModel('core/date')->gmtDate();

				// Assign the data to the model
				$model->setData($data)
					->setId($this->getRequest()->getParam('id'));

				// Category image must be provided
				if (!isset($data['image']))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('general_tab');
					throw new Exception ("You must provide a banner image.");
				}

				// If it's an automatic banner, the start and end dates must be provided
				if($data['status'] == 2 && (!array_key_exists('start_date',$data) || !array_key_exists('end_date',$data) || !$data['start_date'] || !$data['end_date']))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('general_tab');
					throw new Exception ("Start and end dates must be provided for an automatic banner.");
				}

				if ($data['status'] == 2 && isset($data['start_date']) && $data['start_date'])
				{
					// Convert the date properly
					$startdate = Mage::app()->getLocale()->date($data['start_date'], Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
					// Set the times to zero
					$startdate->set('00:00:00',Zend_Date::TIMES);
				}

				if ($data['status'] == 2 && isset($data['end_date']) && $data['end_date'])
				{
					// Convert the date properly
					$enddate = Mage::app()->getLocale()->date($data['end_date'], Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
					// Set the times to zero
					$enddate->set('00:00:00',Zend_Date::TIMES);
				}

				// End date must be later than start date
				if ($data['status'] == 2 && isset($enddate) && isset($startdate) && $startdate->isLater($enddate))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('general_tab');
					throw new Exception ("Start date must not be later than end date.");
				}

				// If it is disabled we don't display it
				if ($data['status'] == 0)
				{
					$model->setDisplayed(0);
				}
				// If it is enabled we do display it
				elseif($data['status'] == 1)
				{
					$model->setDisplayed(1);
				}
				// If it is automatic, it depends on the dates
				elseif($data['status'] == 2)
				{
					// Current date		
					$today = Mage::app()->getLocale()->date();

					// If the start date is earlier than today and end date later than today
					if (isset($startdate) && $startdate->isEarlier($today) && isset($enddate) && $enddate->isLater($today))
					{
						// We display the banner
						$model->setDisplayed(1);
					}
					// If the start date is earlier than today and end date earlier than today 
					// or if the start date is later than today
					elseif(isset($startdate) && (($startdate->isEarlier($today) && isset($enddate) && $enddate->isEarlier($today)) || $startdate->isLater($today)))
					{
						// We don't display the banner
						$model->setDisplayed(0);
					}

					// Set the dates
					if (isset($startdate))  $model->setStartDate($startdate);
					if (isset($enddate))  $model->setEndDate($enddate);
				}

				// Save the banner
				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('categorybanners')->__('Banner was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				// If "continue edit" we redirect back
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				if ($model->getId())
				{
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
				}
				else
				{
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				}
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('categorybanners')->__('Unable to find banner to save'));
		$this->_redirect('*/*/');
	}

	/**
	 *	This is the action called for the mass delete action
     */
	public function massDeleteAction()
	{
		// We retrieve the banner ids
		$bannerIds = $this->getRequest()->getParam('categorybanners');
		// Check if it's an array
		if (!is_array($bannerIds))
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('categorybanners')->__('Please select banner(s)'));
		}
		else
		{
			try {
				// Loop through the banners
				foreach ($bannerIds as $bannerId) {
					// Load then delete
					$model = Mage::getModel('categorybanners/banner')->load($bannerId);
					$model->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('categorybanners')->__(
						'Total of %d banner(s) were successfully deleted', count($bannerIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	/**
	 * This is the action called for the mass status change
     */
	public function massStatusAction()
	{
		// We retrieve the banner ids
		$bannerIds = $this->getRequest()->getParam('categorybanners');
		// Check if it's an array
		if (!is_array($bannerIds))
		{
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select banner(s)'));
		}
		else
		{
			try {
				// Loop through the ids
				foreach ($bannerIds as $bannerId)
				{
					// Load banner
					$banner = Mage::getModel('categorybanners/banner')
						->load($bannerId);
					// If we change the status to disabled we don't display it
					if ($this->getRequest()->getParam('status') == 0)
					{
						$banner->setStatus($this->getRequest()->getParam('status'))
							->setDisplayed(0)
							->setIsMassupdate(true);
					}
					// If we change the status to enabled we do display it
					elseif($this->getRequest()->getParam('status') == 1)
					{
						$banner->setStatus($this->getRequest()->getParam('status'))
							->setDisplayed(1)
							->setIsMassupdate(true);
					}
					// If we change the status to automatic, it depends on the dates
					elseif($this->getRequest()->getParam('status') == 2)
					{
						// Current date		
						$today = Mage::app()->getLocale()->date();
						// Start date
						$startDate = Mage::app()->getLocale()->date($banner->getStartDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
						$startDate->set('00:00:00',Zend_Date::TIMES);
						// End date
						$endDate = Mage::app()->getLocale()->date($banner->getEndDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
						$endDate->set('00:00:00',Zend_Date::TIMES);
						// If the start date is earlier than today and end date later than today
						if ($startDate->isEarlier($today) && $endDate->isLater($today))
						{
							// We display the banner
							$banner->setStatus($this->getRequest()->getParam('status'))
								->setDisplayed(1)
								->setIsMassupdate(true);
						}
						// If the start date is earlier than today and end date earlier than today 
						// or if the start date is later than today
						elseif(($startDate->isEarlier($today) && $endDate->isEarlier($today)) || ($startDate->isLater($today)))
						{
							// We don't display the banner
							$banner->setStatus($this->getRequest()->getParam('status'))
								->setDisplayed(0)
								->setIsMassupdate(true);
						}
					}
					// We save those attribute
                    $banner->getResource()->saveAttribute($banner,array('status','displayed','is_massupdate'));
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($bannerIds))
				);
			} catch (Exception $e) {

				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

}