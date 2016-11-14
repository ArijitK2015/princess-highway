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
class FactoryX_StoreLocator_Adminhtml_Location_ConfigController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/ustorelocator');
    }

    /**
     * @return mixed
     */
    public function exportAction()
    {
        try {
            $website = $this->getRequest()->getParam('website');
            $store = $this->getRequest()->getParam('store');
            $stores = array();
            if($store) {
                $stores[] = $store;
            } else if ($website) {
                $stores = Mage::app()->getWebsite($website)->getStoreCodes();
            }
            /* @var $collection FactoryX_StoreLocator_Model_Mysql4_Location_Collection */
            $collection = Mage::getModel('ustorelocator/location')->getCollection();
            if(!empty($stores)) {
                $select = $collection->getSelect();
                $select->where('`stores`=""')->orWhere('ISNULL(`stores`)'); // if store filter, select non filtered stores,
                foreach($stores as $s) {
                    $select->orWhere('FIND_IN_SET(?, `stores`)', $s); // and then filtered
                }
            }
            $data = $collection->getData();
            if (!empty($data)) {
                $target = Mage::getConfig()->getVarDir('storelocator/export');
                Mage::getConfig()->createDirIfNotExists($target);
                $filename = 'export.' . time() . '.csv';
                $path = $target . DS .$filename;
                $fh = @fopen($path, 'w');
                if (!$fh) {
                    Mage::throwException(Mage::helper('ustorelocator')->__("Could not open %s for writing.", $path));
                }
                $headers = false;
                foreach($data as $line) {
                    if(isset($line['location_id'])) {
                        unset($line['location_id']);
                    }
                    if($headers === false) {
                        $headers = array_keys($line);
                        fputcsv($fh, $headers);
                    }
                    fputcsv($fh, $line);
                }
                fclose($fh);
                return $this->_prepareDownloadResponse($filename, file_get_contents($path), 'text/csv');
            } else {
                $this->_getSession()->addWarning(Mage::helper('ustorelocator')->__("No locations found."));
                $this->_redirect('*/*/');
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/'); // redirect to previous page
        }
    }
}
