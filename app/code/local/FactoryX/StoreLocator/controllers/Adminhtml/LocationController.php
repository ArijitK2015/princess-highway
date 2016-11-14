<?php
/**
 * FactoryX_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @copyright
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @author
 */
class FactoryX_StoreLocator_Adminhtml_LocationController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('factoryx_menu/factoryx_menu_store_locations');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Store Locations'), Mage::helper('adminhtml')->__('Store Locations'));
        $this->_addContent($this->getLayout()->createBlock('ustorelocator/adminhtml_location'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('factoryx_menu/factoryx_menu_store_locations');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Store Locations'), Mage::helper('adminhtml')->__('Store Locations'));

        $this->_addContent($this->getLayout()->createBlock('ustorelocator/adminhtml_location_edit'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->editAction();
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            $req = $this->getRequest();
            $redirectBack  = $req->getParam('back', false);
            $id = $req->getParam('id');
            try {
                $stores = $req->getParam('stores');
                if(is_array($stores)) {
                    $stores = join(',',$stores);
                }
                $icons = $req->getParam('icon');
                $icon = isset($icons['value']) ? $icons['value'] : null;
                if (isset($icons['delete']) && $icons['delete'] == 1) {
                    $file = Mage::getBaseDir('media') . $icon;
                    if (file_exists($file) && is_writable($file)) {
                        unlink($file);
                    } else {
                        $this->_getSession()->addWarning($this->__("Icon file does not exist or cannot be deleted."));
                    }
                    $icon = null;
                } else {
                    if(isset($_FILES['icon']['tmp_name']) && !empty($_FILES['icon']['tmp_name'])) {
                        try {$uploader = new Varien_File_Uploader('icon');
                            $target = $this->getIconsDir();
                            $result = $uploader->setAllowCreateFolders(true)
                                         ->setAllowedExtensions(array('png'))
                                         ->addValidateCallback('size', Mage::helper('ustorelocator/protected'), 'validateIconSize')
                                         ->save($target);
                            $icon = Mage::helper('ustorelocator')->getIconDirPrefix() . DS . $result['file'];

                        } catch (Exception $e) {
                            $this->_getSession()->addWarning($e->getMessage());
                        }
                    }
                }
                $udVendor = $req->getParam('udropship_vendor');

                if(empty($udVendor) && $udVendor !== 0) $udVendor = null;
                $ds = $req->getParam('data_serialized');
                $dataSerialized = json_encode($this->prepareSerializedData($ds));

                $images = $req->getParam('image');
                $image = isset($images['value']) ? $images['value'] : null;
                if (isset($images['delete']) && $images['delete'] == 1) {
                    $file = Mage::getBaseDir('media') . $image;
                    if (file_exists($file) && is_writable($file)) {
                        unlink($file);
                    } else {
                        $this->_getSession()->addWarning($this->__("Image file does not exist or cannot be deleted."));
                    }
                    $image = null;
                } else {
                    if(isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
                        try {$uploader = new Varien_File_Uploader('image');
                            $target = $this->getImagesDir();
                            $result = $uploader->setAllowCreateFolders(true)
                                ->setAllowedExtensions(array('png','jpeg','jpg','gif'))
                                ->save($target);
                            $image = Mage::helper('ustorelocator')->getImageDirPrefix() . DS . $result['file'];

                        } catch (Exception $e) {
                            $this->_getSession()->addWarning($e->getMessage());
                        }
                    }
                }

                if(empty($udVendor) && $udVendor !== 0) $udVendor = null;
                $ds = $req->getParam('data_serialized');
                $dataSerialized = json_encode($this->prepareSerializedData($ds));

                Mage::helper('ustorelocator')->log(sprintf("%s->params: %s", __METHOD__, print_r($req->getParams(), true)) );
                $model = Mage::getModel('ustorelocator/location')
                    //->addData($req->getParams())
                    ->setId($req->getParam('id'))
                    ->setStoreCode($req->getParam('store_code'))
                    ->setStoreType($req->getParam('store_type'))
                    ->setTitle($req->getParam('title'))
                    ->setAddress($req->getParam('address'))
                    ->setNotes($req->getParam('notes'))
                    ->setLongitude($req->getParam('longitude'))
                    ->setLatitude($req->getParam('latitude'))
                    ->setAddressDisplay($req->getParam('address_display'))
                    ->setNotes($req->getParam('notes'))
                    ->setWebsiteUrl($req->getParam('website_url'))
                    ->setIpAddress($req->getParam('ip_address'))
                    ->setPhone($req->getParam('phone'))
					->setRegion($req->getParam('region'))
                    ->setUdropshipVendor($udVendor)
                    ->setCountry($req->getParam('country'))
                    ->setProductTypes($req->getParam('product_types'))
                    ->setIsFeatured($req->getParam('is_featured'))
                    ->setUseLabel($req->getParam('use_label'))
                    ->setZoom($req->getParam('zoom') ? $req->getParam('zoom'): 15) // set default location zoom to 15.
                    ->setStores($stores)
                    ->setDataSerialized($dataSerialized)
                    ->setIcon($icon)
                    ->setImage($image);

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Store location was successfully saved'));
                if ($redirectBack) {
                    if ($model->getId()) {
                        $id = $model->getId();
                    }
                    $this->_redirect('*/*/edit', array('id' => $id));
                } else {
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check submitted serialixed data for any actual values
     * return only non empty data
     *
     * @param array|string $dataSerialized
     * @return array
     */
    public function prepareSerializedData($dataSerialized)
    {
        if (!empty($dataSerialized)) {
            $return = array();
            if (!is_array($dataSerialized)) {
                $dataSerialized = json_decode($dataSerialized);
            }

            foreach ($dataSerialized as $key => $value) {
                if (!empty($value)) {
                    // if value is array
                    if (is_array($value)) {
                        $return[$key] = $this->prepareSerializedData($value);
                    } else { // end if value array
                        $return[$key] = $value;
                    }
                } // end if !empty $value
            } // end for each loop

            if (!empty($return)) {
                return $return;
            }
        } // end empty data check

        return $dataSerialized;
    }

    /**
     * @return mixed
     */
    public function getIconsDir()
    {
        return Mage::helper('ustorelocator')->getIconsDir();
    }

    /**
     * @return mixed
     */
    public function getImagesDir()
    {
        return Mage::helper('ustorelocator')->getImagesDir();
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('ustorelocator/location');
                /* @var $model Mage_Rating_Model_Rating */
                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Store location was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/factoryx_menu_store_locations');
    }

    /**
     * @return bool
     */
    protected function _validateSecretKey()
    {
        if ($this->getRequest()->getActionName() == 'updateEmptyGeoLocations') {
            return true;
        }
        return parent::_validateSecretKey();
    }

    public function updateEmptyGeoLocationsAction()
    {
        Mage::helper('ustorelocator')->populateEmptyGeoLocations();
        exit;
    }

    public function massDeleteAction()
    {
        // implement mass delete action
        $ids = $this->getRequest()->getParam('location');
        try {
            if (!empty($ids)) {
                $collection = $this->getLocationCollection($ids);
                $result = $collection->walk('delete');
                $this->_getSession()->addSuccess($this->__("%d locations deleted.", count($result)));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    public function massCoordinatesAction()
    {
        // implement mass renew coordinates action.
        $ids = $this->getRequest()->getParam('location');
        try {
            if (!empty($ids)) {
                $collection = $this->getLocationCollection($ids);
                /* @var $helper FactoryX_StoreLocator_Helper_Data */
                $helper = Mage::helper('ustorelocator');
                $result = $helper->populateEmptyGeoLocations($collection);
                if (false === $result) {
                    $this->_getSession()->addError($this->__("Coordinates not updated, check '%s' for details.", $helper->getLogFile()));
                }
                else {
                    $this->_getSession()->addSuccess($this->__("Coordinates of %d locations updated.", $result));
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * @param $ids
     * @return FactoryX_StoreLocator_Model_Mysql4_Location_Collection
     */
    protected function getLocationCollection($ids)
    {
        /* @var $collection FactoryX_StoreLocator_Model_Mysql4_Location_Collection */
        $collection = Mage::getModel('ustorelocator/location')->getCollection();
        $collection->addFieldToFilter('location_id', array('in' => $ids));
        return $collection;
    }

    public function reinstallAction()
    {
        try {
            /* @var $installer FactoryX_StoreLocator_Model_Resource_Setup */
            $installer = new FactoryX_StoreLocator_Model_Resource_Setup('ustorelocator_setup');
            $installer->reinstall();
            $this->_getSession()->addSuccess($this->__("Module DB files reinstalled"));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
