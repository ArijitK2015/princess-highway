<?php

class FactoryX_Homepage_Adminhtml_HomepageController extends Mage_Adminhtml_Controller_Action
{
	
	protected function _initAction() 
	{
        $this->loadLayout()->_setActiveMenu('factoryx_menu/homepage');

        return $this;
    }

    public function indexAction() 
	{
	    try {
            $this->_initAction()->renderLayout();
        }
        catch(Exception $ex) {
            Mage::helper('homepage')->(sprintf("%s->error=%s", __METHOD__, print_r($ex, true)), Zend_Log::DEBUG );
        }
    }
	
	public function deleteAction() {
        $homepageId = (int) $this->getRequest()->getParam('id');
        if ($homepageId) {
            try {
                $model = Mage::getModel('homepage/homepage')->load($homepageId);
				$model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('homepage')->__('Homepage was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	
	public function newAction() 
	{
	    $data = $this->getRequest()->getPost();
		if ($data) {
		    Mage::log(sprintf("%s->data=%s", __METHOD__, print_r($data, true)), Zend_Log::DEBUG );
		    
		    if (!array_key_exists('layout', $data)) {
	    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('homepage')->__('No layout was choosen'));
                $this->_redirect('*/*/');        
		    }
		    else {
    			// If previous page has been filled we add the layout to the session
    			Mage::getSingleton('adminhtml/session')->setLayout($data['layout']);
    			// If an ID is provided, that means it is an existing homepage and there is a layout change
    			if ($id = $this->getRequest()->getParam('id')) 
    				// We set a flag
    				Mage::getSingleton('adminhtml/session')->setChangingLayout(1);
    			$this->_forward('edit'); 
    		}
		}
		else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('homepage')->__('Home Page does not exist'));
            $this->_redirect('*/*/');
		}
    }
	
	public function detailsAction() 
	{	
		// If an ID is provided, there is a layout change
        if ($id = $this->getRequest()->getParam('id')) 
		{
			// We load the existing homepage
			$model = Mage::getModel('homepage/homepage')->load($id);
			if ($model->getId())
			{
				// We use sessions to pass parameters to the next page
				Mage::getSingleton('adminhtml/session')->setAmount($model->getAmount());
				Mage::getSingleton('adminhtml/session')->setSlider($model->getSlider());
			}
		}
		
		$this->loadLayout();
		$this->_setActiveMenu('factoryx_menu/homepage');

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('homepage/adminhtml_homepage_details'));

		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

		$this->renderLayout();
    }
	
	public function chooselayoutAction()
	{
		
		$session = Mage::getSingleton('adminhtml/session');
		
		// Translation mode
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);
		try
		{
			if ($data = $this->getRequest()->getPost()) 
			{
				// If the amount of pictures chosen is more than 5
				if ($data['amount'] > 5)
				{
					Mage::throwException($this->__('The module does not support more than 5 pictures, please contact the developers for more information.'));
				}
				
				// If an ID is provided, that means there is a layout change
				if ($id = $this->getRequest()->getParam('id'))
				{
					$model = Mage::getModel('homepage/homepage')->load($id);
					if ($model->getId())
					{
						// Compare the new amount and the new slider setting
						if ($data['amount'] == $model->getAmount() && $data['slider'] == $model->getSlider())
						{
							// If they're not different, we set the layout with the current homepage layout
							$session->setLayout($model->getLayout());
						}
					}
				}
				
				// Add data to session
				$session->setAmount($data['amount']);
				$session->setSlider($data['slider']);
				
				$this->loadLayout();
				$this->_setActiveMenu('factoryx_menu/homepage');

				$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

				$this->_addContent($this->getLayout()->createBlock('homepage/adminhtml_homepage_chooselayout'));

				$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

				$this->renderLayout();
				
				$translate->setTranslateInline(true);
				
			} 
			else 
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('homepage')->__('Homepage does not exist'));
				$this->_redirect('*/*/index');
			}
		}
		catch (Exception $e) 
		{
			$errorMessage = "";
			$errorMessage = $e->getMessage();
        	Mage::helper('homepage')->log("Error: " . $errorMessage);
            $translate->setTranslateInline(true);
            $session->addError($errorMessage);
			$this->_redirectReferer();
			return;
        }
	}
	
	public function editAction() 
	{
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('homepage/homepage')->load($id);

        if ($model->getId() || $id == 0) 
		{
			// Data from the form
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			
			// If it is a new home page
			if ($id == 0)
			{
				// We retrieve all possible session variables set in the previous steps
				if (!is_array($data) || (!array_key_exists('amount',$data)))
				{
					$data['amount'] = Mage::getSingleton('adminhtml/session')->getAmount(true);
				}
				
				if (!is_array($data) || (!array_key_exists('slider',$data)))
				{
					$data['slider'] = Mage::getSingleton('adminhtml/session')->getSlider(true);
				}
				
				if (!is_array($data) || (!array_key_exists('layout',$data)))
				{
					$data['layout'] = Mage::getSingleton('adminhtml/session')->getLayout(true);
				}
			}
			// If it is an existing home page
			else
			{
				// And if the changing layout flag is set
				if (Mage::getSingleton('adminhtml/session')->getChangingLayout(true))
				{
					// This is a flag
					$saveFlag = false;
					// This is the new amount of pictures
					$amount = Mage::getSingleton('adminhtml/session')->getAmount(true);
					
					// We test the org and the new amount
					if ($amount != $model->getAmount())
					{
						// Unset the extra images if we switching to a lower amount
						if ($model->getAmount() > $amount)
						{
							$count = $amount;
							$count++;
							for($count; $count <= $model->getAmount();$count++)
							{
								$imageToDelete = $model->getImage($count);
								$imageToDelete->delete();
							}
						}
						
						// Change the amount if needed
						$model->setAmount($amount);
						$saveFlag = true;
					}
					
					// This is the new slider setting
					$slider = Mage::getSingleton('adminhtml/session')->getSlider(true);
					
					// We test the org and the new slider
					if ($slider != $model->getSlider())
					{
						// Change it if needed
						$model->setSlider($slider);
						$saveFlag = true;
					}
					
					// This is the new layout
					$layout = Mage::getSingleton('adminhtml/session')->getLayout(true);
					
					// We test the org and the new layout
					if ($layout != $model->getLayout())
					{
						// Change it if needed
						$model->setLayout($layout);
						$saveFlag = true;
					}
					
					// If the flag set, we save the homepage
					if ($saveFlag)
					{
						$model->save();
					}
				}
			}
			
			// If data from the form, we add it to the model
            if (!empty($data)) {
                $model->setData($data);
            }
			
            Mage::register('homepage_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('factoryx_menu/homepage');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('homepage/adminhtml_homepage_edit'))
					->_addLeft($this->getLayout()->createBlock('homepage/adminhtml_homepage_edit_tabs'));

			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);


            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('homepage')->__('Home Page does not exist'));
            $this->_redirect('*/*/');
        }
    }
	
	public function saveAction() 
	{
        if ($data = $this->getRequest()->getPost()) 
		{
            $model = Mage::getModel('homepage/homepage');
			
			try 
			{
				
				// Loop based on the amount of pictures chosen
				for($i=1;$i<=$data['amount'];$i++)
				{
					// Before checking for errors we need to process the image data so they won't be lost in case of errors
					// Save the frontend homepage image
					if(isset($_FILES['image_'.$i]['name']) and (file_exists($_FILES['image_'.$i]['tmp_name']))) 
					{
						$uploader = new Mage_Core_Model_File_Uploader('image_'.$i);
						$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(true);
						$uploader->setFilesDispersion(true);
						$result = $uploader->save(
							Mage::getSingleton('homepage/homepage_media_config')->getBaseMediaPath()
						);

						// Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
						$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
						$result['path'] = str_replace(DS, "/", $result['path']);

						$result['url'] = Mage::getSingleton('homepage/homepage_media_config')->getMediaUrl($result['file']);
						$result['cookie'] = array(
							'name'     => session_name(),
							'value'    => $this->_getSession()->getSessionId(),
							'lifetime' => $this->_getSession()->getCookieLifetime(),
							'path'     => $this->_getSession()->getCookiePath(),
							'domain'   => $this->_getSession()->getCookieDomain()
						);
						
						$data['image_'.$i] = $result['file'];
					}
					else 
					{       
						if(isset($data['image_'.$i]['delete']) && $data['image_'.$i]['delete'] == 1)
						{
							$data['image_'.$i] = NULL;
						}
					}
					
					if (isset($data['image_'.$i]))
					{
						if (is_array($data['image_'.$i]))
						{
							$data['image_'.$i] = $data['image_'.$i]['value'];
						}
					}
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
				
				// Edited date
				$data['edited'] = Mage::getModel('core/date')->gmtDate();
				
				// Assign the data to the model
				$model->setData($data)
						->setId($this->getRequest()->getParam('id'));
				
				// Handle errors
				// Images must be provided
				for($i=1;$i<=$data['amount'];$i++)
				{
					if (!isset($data['image_'.$i]))
					{
						// We use session to set the active tab to show where the error is
						Mage::getSingleton('admin/session')->setActiveTab('media_tab');
						throw new Exception ("You must provide home page images.");
					}
				}
				
				// If it's an automatic homepage, the start and end dates must be provided
				if($data['status'] == 2 && (!isset($data['start_date']) || !isset($data['end_date'])))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('general_tab');
					throw new Exception ("Start and end dates must be provided for an automatic homepage.");
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
					if ($startdate->isEarlier($today) && $enddate->isLater($today))
					{
						// We display the homepage
						$model->setDisplayed(1);
					}
					// If the start date is earlier than today and end date earlier than today 
					// or if the start date is later than today
					elseif(($startdate->isEarlier($today) && $enddate->isEarlier($today)) || ($startdate->isLater($today)))
					{
						// We don't display the homepage
						$model->setDisplayed(0);
					}
				}

				// Save the homepage
                $model->save();		

				// Save the images to the database
				for($i=1;$i<=$data['amount'];$i++)
				{
					// Retrieve possible existing images for this home page
					$existingImage = $model->getImage($i);
					// If there is no existing images for this home page in the database
					if (!$existingImage)
					{
						// We create the images
						$pictureModel = Mage::getModel('homepage/image');
						$pictureModel->setUrl($data['image_'.$i]);
						$pictureModel->setLink($data['link_'.$i]);
						$pictureModel->setAlt($data['alt_'.$i]);
						$pictureModel->setPopup($data['popup_'.$i]);
						$pictureModel->setIndex($i);
						$pictureModel->setHomepageId($model->getId());
						$pictureModel->save();
					}
					else
					{
						// We replace the old image with the new one
						$existingImage->setUrl($data['image_'.$i]);
						$existingImage->setLink($data['link_'.$i]);
						$existingImage->setAlt($data['alt_'.$i]);
						$existingImage->setPopup($data['popup_'.$i]);
						$existingImage->save();
					}
				}
				
				// Clean cache of the homepages in case status have changed
				Mage::app()->cleanCache(FactoryX_Homepage_Model_Homepage::CACHE_TAG);
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('homepage')->__('Home Page was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('homepage')->__('Unable to find homepage to save'));
        $this->_redirect('*/*/');
    }
	
	public function massDeleteAction() 
	{
        $homepageIds = $this->getRequest()->getParam('homepage');
        if (!is_array($homepageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select homepage(s)'));
        } else {
            try {
                foreach ($homepageIds as $homepageId) {
                    $model = Mage::getModel('homepage/homepage')->load($homepageId);
					$model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($homepageIds)
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
        $homepageIds = $this->getRequest()->getParam('homepage');
        if (!is_array($homepageIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select homepage(s)'));
        } else {
            try {

				foreach ($homepageIds as $homepageId) 
				{
					// Load homepage
					$homepage = Mage::getModel('homepage/homepage')
                            ->load($homepageId);
					// If it is disabled we don't display it
					if ($this->getRequest()->getParam('status') == 0)
					{
						$homepage->setStatus($this->getRequest()->getParam('status'))
								->setDisplayed(0)
								->setStores('')
								->setIsMassupdate(true)
								->save();
					}
					// If it is enabled we do display it
					elseif($this->getRequest()->getParam('status') == 1)
					{
						$homepage->setStatus($this->getRequest()->getParam('status'))
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
						$startDate = Mage::app()->getLocale()->date($homepage->getStartDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
						$startDate->set('00:00:00',Zend_Date::TIMES);
						// End date
						$endDate = Mage::app()->getLocale()->date($homepage->getEndDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
						$endDate->set('00:00:00',Zend_Date::TIMES);
						// If the start date is earlier than today and end date later than today
						if ($startDate->isEarlier($today) && $endDate->isLater($today))
						{
							// We display the homepage
							$homepage->setStatus($this->getRequest()->getParam('status'))
									->setDisplayed(1)
									->setStores('')
									->setIsMassupdate(true)
									->save();
						}
						// If the start date is earlier than today and end date earlier than today 
						// or if the start date is later than today
						elseif(($startDate->isEarlier($today) && $endDate->isEarlier($today)) || ($startDate->isLater($today)))
						{
							// We don't display the homepage
							$homepage->setStatus($this->getRequest()->getParam('status'))
									->setDisplayed(0)
									->setStores('')
									->setIsMassupdate(true)
									->save();
						}
					}
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($homepageIds))
                );
            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	/**
     * Create homepage duplicate
     */
    public function duplicateAction()
    {
        $homepageId  = (int) $this->getRequest()->getParam('id');
		$homepage = Mage::getModel('homepage/homepage')->load($homepageId);
		
        try {
            $newHomepage = $homepage->duplicate();
            $this->_getSession()->addSuccess($this->__('The homepage has been duplicated.'));
            $this->_redirect('*/*/edit', array('_current'=>true, 'id'=>$newHomepage->getId()));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('_current'=>true));
        }
    }

}