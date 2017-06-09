<?php

/**
 * Class FactoryX_Contests_Adminhtml_ContestsController
 */
class FactoryX_Contests_Adminhtml_ContestsController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * @return mixed
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/contests');
	}

	/**
	 * @return $this
	 */
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('factoryx_menu/contests');

		return $this;
	}

	public function referrersAction()
	{
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('contests/adminhtml_referrers'));
		$this->renderLayout();
	}

	public function refereesAction()
	{
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('contests/adminhtml_referees'));
		$this->renderLayout();
	}

	public function indexAction()
	{
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('contests/adminhtml_contests'));
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function deleteAction() {
		$contestId = (int) $this->getRequest()->getParam('id');
		if ($contestId) {
			try {
				$model = Mage::getModel('contests/contest')->load($contestId);
				$model->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('contests')->__('Contest was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('contests/contest')->load($id);

		if ($model->getId() || $id == 0)
		{
			$url = $model->getUrlInStore();

			Mage::app()->getResponse()->setRedirect($url);
			Mage::app()->getResponse()->sendResponse();

		}
		else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contests')->__('Contest does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('contests/contest')->load($id);

		if ($model->getId() || $id == 0)
		{
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('contests_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('factoryx_menu/contests');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('contests/adminhtml_contests_edit'))
				->_addLeft($this->getLayout()->createBlock('contests/adminhtml_contests_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contests')->__('Contest does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost())
		{
			$model = Mage::getModel('contests/contest');

			try
			{
				// Before checking for errors we need to process the image data so they won't be lost in case of errors
				// Save the frontend contest image
				if(isset($_FILES['image_url']['name']) and (file_exists($_FILES['image_url']['tmp_name'])))
				{
					$uploader = new Mage_Core_Model_File_Uploader('image_url');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$result = $uploader->save(
						Mage::getSingleton('contests/contest_media_config')->getBaseMediaPath()
					);

					/**
					 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
					 */
					$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
					$result['path'] = str_replace(DS, "/", $result['path']);

					$result['url'] = Mage::getSingleton('contests/contest_media_config')->getMediaUrl($result['file']);
					$result['cookie'] = array(
						'name'     => session_name(),
						'value'    => $this->_getSession()->getSessionId(),
						'lifetime' => $this->_getSession()->getCookieLifetime(),
						'path'     => $this->_getSession()->getCookiePath(),
						'domain'   => $this->_getSession()->getCookieDomain()
					);

					$data['image_url'] = $result['file'];
				}
				else
				{
					if(isset($data['image_url']['delete']) && $data['image_url']['delete'] == 1)
					{
						$data['image_url'] = NULL;
					}
				}

				if (isset($data['image_url']))
				{
					if (is_array($data['image_url']))
					{
						$data['image_url'] = $data['image_url']['value'];
					}
					$model->setImageUrl($data['image_url']);
				}

				// Save the email contest picture
				if(isset($_FILES['email_image_url']['name']) and (file_exists($_FILES['email_image_url']['tmp_name'])))
				{
					$uploader = new Mage_Core_Model_File_Uploader('email_image_url');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));

					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$result = $uploader->save(
						Mage::getSingleton('contests/contest_media_config')->getBaseMediaPath()
					);

					/**
					 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
					 */
					$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
					$result['path'] = str_replace(DS, "/", $result['path']);

					$result['url'] = Mage::getSingleton('contests/contest_media_config')->getMediaUrl($result['file']);
					$result['cookie'] = array(
						'name'     => session_name(),
						'value'    => $this->_getSession()->getSessionId(),
						'lifetime' => $this->_getSession()->getCookieLifetime(),
						'path'     => $this->_getSession()->getCookiePath(),
						'domain'   => $this->_getSession()->getCookieDomain()
					);

					$data['email_image_url'] = $result['file'];
				}
				else
				{
					if(isset($data['email_image_url']['delete']) && $data['email_image_url']['delete'] == 1)
					{
						$data['email_image_url'] = NULL;
					}
				}

				if (isset($data['email_image_url']))
				{
					if (is_array($data['email_image_url']))
					{
						$data['email_image_url'] = $data['email_image_url']['value'];
					}
					$model->setEmailImageUrl($data['email_image_url']);
				}

				// Save the list contest picture
				if(isset($_FILES['list_image_url']['name']) and (file_exists($_FILES['list_image_url']['tmp_name'])))
				{
					$uploader = new Mage_Core_Model_File_Uploader('list_image_url');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$result = $uploader->save(
						Mage::getSingleton('contests/contest_media_config')->getBaseMediaPath()
					);

					/**
					 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
					 */
					$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
					$result['path'] = str_replace(DS, "/", $result['path']);

					$result['url'] = Mage::getSingleton('contests/contest_media_config')->getMediaUrl($result['file']);
					$result['cookie'] = array(
						'name'     => session_name(),
						'value'    => $this->_getSession()->getSessionId(),
						'lifetime' => $this->_getSession()->getCookieLifetime(),
						'path'     => $this->_getSession()->getCookiePath(),
						'domain'   => $this->_getSession()->getCookieDomain()
					);

					$data['list_image_url'] = $result['file'];
				}
				else
				{
					if(isset($data['list_image_url']['delete']) && $data['list_image_url']['delete'] == 1)
					{
						$data['list_image_url'] = NULL;
					}
				}

				if (isset($data['list_image_url']))
				{
					if (is_array($data['list_image_url']))
					{
						$data['list_image_url'] = $data['list_image_url']['value'];
					}
					$model->setListImageUrl($data['list_image_url']);
				}

				// Save the thank you contest picture
				if(isset($_FILES['thank_you_image_url']['name']) and (file_exists($_FILES['thank_you_image_url']['tmp_name'])))
				{
					$uploader = new Mage_Core_Model_File_Uploader('thank_you_image_url');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$result = $uploader->save(
						Mage::getSingleton('contests/contest_media_config')->getBaseMediaPath()
					);

					/**
					 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
					 */
					$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
					$result['path'] = str_replace(DS, "/", $result['path']);

					$result['url'] = Mage::getSingleton('contests/contest_media_config')->getMediaUrl($result['file']);
					$result['cookie'] = array(
						'name'     => session_name(),
						'value'    => $this->_getSession()->getSessionId(),
						'lifetime' => $this->_getSession()->getCookieLifetime(),
						'path'     => $this->_getSession()->getCookiePath(),
						'domain'   => $this->_getSession()->getCookieDomain()
					);

					$data['thank_you_image_url'] = $result['file'];
				}
				else
				{
					if(isset($data['thank_you_image_url']['delete']) && $data['thank_you_image_url']['delete'] == 1)
					{
						$data['thank_you_image_url'] = NULL;
					}
				}

				if (isset($data['thank_you_image_url']))
				{
					if (is_array($data['thank_you_image_url']))
					{
						$data['thank_you_image_url'] = $data['thank_you_image_url']['value'];
					}
					$model->setThankYouImageUrl($data['thank_you_image_url']);
				}

				// Save the popup contest picture
				if(isset($_FILES['popup_image_url']['name']) and (file_exists($_FILES['popup_image_url']['tmp_name'])))
				{
					$uploader = new Mage_Core_Model_File_Uploader('popup_image_url');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$result = $uploader->save(
						Mage::getSingleton('contests/contest_media_config')->getBaseMediaPath()
					);

					/**
					 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
					 */
					$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
					$result['path'] = str_replace(DS, "/", $result['path']);

					$result['url'] = Mage::getSingleton('contests/contest_media_config')->getMediaUrl($result['file']);
					$result['cookie'] = array(
						'name'     => session_name(),
						'value'    => $this->_getSession()->getSessionId(),
						'lifetime' => $this->_getSession()->getCookieLifetime(),
						'path'     => $this->_getSession()->getCookiePath(),
						'domain'   => $this->_getSession()->getCookieDomain()
					);

					$data['popup_image_url'] = $result['file'];
				}
				else
				{
					if(isset($data['popup_image_url']['delete']) && $data['popup_image_url']['delete'] == 1)
					{
						$data['popup_image_url'] = NULL;
					}
				}

				if (isset($data['popup_image_url']))
				{
					if (is_array($data['popup_image_url']))
					{
						$data['popup_image_url'] = $data['popup_image_url']['value'];
					}
					$model->setPopupImageUrl($data['popup_image_url']);
				}

				// Check is single store mode
				if (!Mage::app()->isSingleStoreMode() && isset($data['stores']))
				{
					if ($data['stores'][0] == 0)
					{
						unset($data['stores']);
						$data['stores'] = array();
						$stores = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
						foreach ($stores as $store)
							$data['stores'][] = $store->getId();
					}
				}

				// Assign the data to the model
				$model->setData($data)
					->setId($this->getRequest()->getParam('id'));

				// Handle errors
				// A contest image must be provided
				if (!isset($data['image_url']))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('media_tab');
					throw new Exception ("You must provide a contest image.");
				}

				// If it's a refer a friend contest, an email image must be provided
				if ($data['type'] == 1 && !isset($data['email_image_url']))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('media_tab');
					throw new Exception ("You must provide a email image for a refer a friend contest");
				}

				// If the contest is displayed in the listing, a list image must be provided
				if ($data['is_in_list'] == 1 && !isset($data['list_image_url']))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('media_tab');
					throw new Exception ("You must provide a list image for a contest displayed in the listing");
				}

				// If the contest is displayed in the listing, a list text must be provided
				if ($data['is_in_list'] == 1 && !isset($data['list_text']) && !$data['list_text'])
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('list_tab');
					throw new Exception ("You must provide a list text for a contest displayed in the listing");
				}

				// If it's a competition contest, a competition text must be provided
				if ($data['is_competition'] && !$data['competition_text'])
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('competition_tab');
					throw new Exception ("You must provide a competition text for a competition contest");
				}

				// If it's a competition contest, a maximum word count must be provided
				if ($data['is_competition'] && $data['maximum_word_count'] <= 0)
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('competition_tab');
					throw new Exception ("You must provide a maximum word count for a competition contest");
				}

				// If the popup is enabled, a popup text must be provided
				if ($data['is_popup'] && !$data['popup_text'])
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('popup_tab');
					throw new Exception ("You must provide a popup text for a contest displayed in a popup");
				}

				// If it's an automatic contest, the start and end dates must be provided
				if($data['status'] == 2 && (!$data['start_date'] || !$data['end_date']))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('general_tab');
					throw new Exception ("Start and end dates must be provided for an automatic contest.");
				}

				if ($data['status'] == 2 && isset($data['start_date']) && $data['start_date'])
				{
					// Convert the date properly
					$startdate = Mage::app()->getLocale()->date($data['start_date'], Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),null, false);
				}
				else
				{
					$startdate = NULL;
				}

				if ($data['status'] == 2 && isset($data['end_date']) && $data['end_date'])
				{
					// Convert the date properly
					$enddate = Mage::app()->getLocale()->date($data['end_date'], Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),null, false);
				}
				else
				{
					$enddate = NULL;
				}

				// End date must be later than start date
				if ($data['status'] == 2 && isset($enddate) && isset($startdate) && $startdate->isLater($enddate))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('general_tab');
					throw new Exception ("Start date must not be later than end date.");
				}

				$model->setStartDate($startdate);
				$model->setEndDate($enddate);

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
					if ($startdate->isEarlier($today) && $enddate->isLater($today))
					{
						// We display the contest
						$model->setDisplayed(1);
					}
					// If the start date is earlier than today and end date earlier than today 
					// or if the start date is later than today
					elseif(($startdate->isEarlier($today) && $enddate->isEarlier($today)) || ($startdate->isLater($today)))
					{
						// We don't display the contest
						$model->setDisplayed(0);
					}
				}

				// Saving colour options
				if ($data['background_colour']) $model->setBackgroundColour($data['background_colour']);
				if ($data['text_colour']) $model->setTextColour($data['text_colour']);
				if ($data['button_background_colour']) $model->setButtonBackgroundColour($data['button_background_colour']);
				if ($data['button_text_colour']) $model->setButtonTextColour($data['button_text_colour']);
				if ($data['custom_css']) $model->setCustomCss($data['custom_css']);
				if ($data['more_friend_line']) $model->setMoreFriendLine($data['more_friend_line']);
				if (array_key_exists('states',$data) && $data['states']) $model->setStates(implode(',',$data['states']));

				// Get the ID of the model or the next possible ID
				$nextId = $model->getId() ? $model->getId() : Mage::helper('contests')->getNextId();

				// Generate the event array
				$eventData = array('id'	=> $nextId, 'contest' => $data);

				// Dispatch the event
				Mage::dispatchEvent('contests_save_before',$eventData);

				$model->save();

				// Disable previous popup contest if popup is set to yes
				if (!Mage::app()->isSingleStoreMode() && isset($data['stores']) && $data['is_popup'])
				{
					foreach ($data['stores'] as $key => $storeId)
					{
						$popupContests = Mage::getResourceModel('contests/contest_collection')
							->addIsPopupFilter(1)
							->addNotIdsFilter($model->getId())
							->addStoreFilter($storeId);

						if (count($popupContests))
						{
							foreach ($popupContests as $previousPopup)
							{
								$previousPopup->setIsPopup(0);
								$previousPopup->save();
							}
						}
					}
				}
				elseif ($data['is_popup'])
				{
					$popupContests = Mage::getResourceModel('contests/contest_collection')
						->addIsPopupFilter(1)
						->addNotIdsFilter($model->getId());

					if (count($popupContests))
					{
						foreach ($popupContests as $previousPopup)
						{
							$previousPopup->setIsPopup(0);
							$previousPopup->save();
						}
					}
				}

				// Clean cache of the contests in case status have changed
				Mage::app()->cleanCache(FactoryX_Contests_Model_Contest::CACHE_TAG);

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('contests')->__('Contest was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contests')->__('Unable to find contest to save'));
		$this->_redirect('*/*/');
	}

	public function massDeleteAction()
	{
		$contestIds = $this->getRequest()->getParam('contests');
		if (!is_array($contestIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select contest(s)'));
		} else {
			try {
				foreach ($contestIds as $contestId) {
					$model = Mage::getModel('contests/contest')->load($contestId);
					$model->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($contestIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massStatusAction()
	{
		$contestIds = $this->getRequest()->getParam('contests');
		if (!is_array($contestIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select contest(s)'));
		} else {
			try {

				foreach ($contestIds as $contestId)
				{
					// Load contest
					$contest = Mage::getModel('contests/contest')
						->load($contestId);
					// If it is disabled we don't display it
					if ($this->getRequest()->getParam('status') == 0)
					{
						$contest->setStatus($this->getRequest()->getParam('status'))
							->setDisplayed(0)
							->setStores('')
							->setIsMassupdate(true)
							->save();
					}
					// If it is enabled we do display it
					elseif($this->getRequest()->getParam('status') == 1)
					{
						$contest->setStatus($this->getRequest()->getParam('status'))
							->setDisplayed(1)
							->setStores('')
							->setIsMassupdate(true)
							->save();
					}
					// If it is automatic, it depends on the dates
					elseif($this->getRequest()->getParam('status') == 2)
					{
						// Current date		
						$today = Mage::app()->getLocale()->date();
						// Start date
						$startDate = Mage::app()->getLocale()->date($contest->getStartDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
						$startDate->set('00:00:00',Zend_Date::TIMES);
						// End date
						$endDate = Mage::app()->getLocale()->date($contest->getEndDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
						$endDate->set('00:00:00',Zend_Date::TIMES);
						// If the start date is earlier than today and end date later than today
						if ($startDate->isEarlier($today) && $endDate->isLater($today))
						{
							// We display the contest
							$contest->setStatus($this->getRequest()->getParam('status'))
								->setDisplayed(1)
								->setStores('')
								->setIsMassupdate(true)
								->save();
						}
						// If the start date is earlier than today and end date earlier than today 
						// or if the start date is later than today
						elseif(($startDate->isEarlier($today) && $endDate->isEarlier($today)) || ($startDate->isLater($today)))
						{
							// We don't display the contest
							$contest->setStatus($this->getRequest()->getParam('status'))
								->setDisplayed(0)
								->setStores('')
								->setIsMassupdate(true)
								->save();
						}
					}
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($contestIds))
				);
			} catch (Exception $e) {

				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function drawAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('contests/contest')->load($id);

		if ($model->getId() || $id == 0)
		{
			$this->loadLayout();
			$this->_setActiveMenu('factoryx_menu/contests');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('contests/adminhtml_contests_draw'))
				->_addLeft($this->getLayout()->createBlock('contests/adminhtml_contests_draw_tabs'));

			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);


			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contests')->__('Contest does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function drawWinnerAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('contests/contest')->load($id);

		if (($model->getId() || $id == 0) && $data = $this->getRequest()->getPost())
		{
			try
			{
				$numberToDraw = empty($data['amount'])? 1:$data['amount'] ;
				$stateDraw = empty($data['state_enable'])? false: $data['state_enable'];
				$states = empty($data['states'])? false:$data['states'];
				$state = empty($data['state'])? false:$data['state'];
				$onePerState = empty($data['one_per_state'])? false:$data['one_per_state'];

				if ($stateDraw && !$state && !$onePerState)
				{
					throw new Exception('Please choose a state');
				}

				// First case: no state selection
				if ($numberToDraw && !$stateDraw)
				{
					$winnersDrawn = $model->drawWinners($numberToDraw);
				}
				// Second case: select amount per ONE state selected
				elseif ($numberToDraw && $stateDraw && $state && !$onePerState)
				{
					$winnersDrawn = $model->drawWinners($numberToDraw,$state);
				}
				// Third case: one per state
				elseif ($numberToDraw && $onePerState && $states)
				{
					$winnersDrawn = $model->drawWinners($numberToDraw,$states);
				}
				else
				{
					throw new Exception ("There has been an issue drawing the winners");
				}

				$this->_getSession()->addSuccess(
					$this->__('%d winner(s) have been drawn', $winnersDrawn)
				);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/draw', array('id' => $this->getRequest()->getParam('id')));
				return;
			}

		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contests')->__('Contest does not exist'));
			$this->_redirect('*/*/index');
		}
	}

	/**
	 * Export referrers grid to CSV format
	 */
	public function exportCsvAction()
	{
		$contestsTitle = "";

		// createBlock(CLASS_GROUP_NAME/PATH_TO_BLOCK_FILE)
		$grid = $this->getLayout()->createBlock('contests/adminhtml_referrers_grid');

		// check if there is a filter then get it for file name
		$filter = $grid->getParam('filter');
		$filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
		if (array_key_exists('contest_title', $filter_data)) {
			$contestsTitle = strtolower(preg_replace("/[^A-Za-z0-9 ]/", '', $filter_data['contest_title']));
		}
		if (!empty($contestsTitle)) {
			$contestsTitle = sprintf("-%s", $contestsTitle);
		}
		$fileName   = sprintf("%s%s-referrers.csv", date("Ymdhis"), $contestsTitle);
		// FactoryX_Contests_Block_Adminhtml_Referrers_Grid
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	}

	/**
	 *  Export referrers grid to Excel XML format
	 */
	public function exportExcelAction()
	{
		$contestsTitle = "";

		// createBlock(CLASS_GROUP_NAME/PATH_TO_BLOCK_FILE)
		$grid = $this->getLayout()->createBlock('contests/adminhtml_referrers_grid');

		// check if there is a filter then get it for file name
		$filter = $grid->getParam('filter');
		$filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
		if (array_key_exists('contest_title', $filter_data)) {
			$contestsTitle = strtolower(preg_replace("/[^A-Za-z0-9 ]/", '', $filter_data['contest_title']));
		}
		if (!empty($contestsTitle)) {
			$contestsTitle = sprintf("-%s", $contestsTitle);
		}
		$fileName   = sprintf("%s%s-referrers.csv", date("Ymdhis"), $contestsTitle);
		// FactoryX_Contests_Block_Adminhtml_Referrers_Grid
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}

}