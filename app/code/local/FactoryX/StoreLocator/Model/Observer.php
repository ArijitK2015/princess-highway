<?php

/**
 * Class FactoryX_StoreLocator_Model_Observer
 */
class FactoryX_StoreLocator_Model_Observer
{
    public function createUrlRewrite()
    {
        $url = Mage::getStoreConfig('ustorelocator/general/page_url');
        if ($url) {
            $rewrites = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('id_path', array('like' => 'ustorelocator-custom'));
            if (sizeof($rewrites) > 0) {
                foreach($rewrites as $rewrite){
                    $rewrite->setData('request_path', $url);
                    $rewrite->save();
                }
            } else {
                $stores = Mage::app()->getStores();
                foreach($stores as $store){
                    $rewrite = Mage::getModel('core/url_rewrite');
                    $rewrite->setData('id_path','ustorelocator-custom');
                    $rewrite->setData('is_system',false);
                    $rewrite->setData('store_id',$store->getId());
                    $rewrite->setData('request_path',$url);
                    $rewrite->setData('target_path','ustorelocator/location/map');
                    $rewrite->save();
                }
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function registerController(Varien_Event_Observer $observer)
    {
        $action = $observer->getControllerAction()->getFullActionName();
        switch ($action)
        {
            case 'adminhtml_permissions_user_index':
                Mage::register('adminhtml_permissions_user_index', true);
                break;
        }

        return $this;
    }

    /**
     * beforeEavCollectionLoad
     *
     *
     * @param Varien_Event_Observer $observer
     * @internal param $ (type) (name) about this param
     */
    public function beforeCoreCollectionLoad(Varien_Event_Observer $observer)
    {

        if(Mage::registry('adminhtml_permissions_user_index')) {
            $collection = $observer->getCollection();
            if (!isset($collection)) {
                return;
            }

            if ($collection instanceof Mage_Admin_Model_Resource_User_Collection) {
                // Add store name
                $collection->getSelect()->joinLeft(
                    array('ustore' => Mage::getSingleton("core/resource")->getTableName('ustorelocator_location')),
                    'main_table.location_id = ustore.location_id',
                    array('ustore.title')
                );
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function blockHtmlBefore(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!isset($block)) {
            return $this;
        }
        if ($block instanceof Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main) {
            // Get form instance
            $form = $block->getForm();
            // Get fieldset
            $fieldset = $form->getElement('base_fieldset');
            // Add stores
            $fieldset->addField('store', 'select', array(
                'name' => 'store',
                'label' => Mage::helper('adminhtml')->__('Default Store'),
                'id' => 'store',
                'title' => Mage::helper('adminhtml')->__('Default Store'),
                'class' => 'required-entry',
                'required' => true,
                'options' => Mage::helper('ustorelocator')->getLocationOptions()
            ));
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function blockCreateAfter(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Permissions_User_Grid) {
            // Add store name
            $block->addColumnAfter(
                'title',
                array(
                    'header' => Mage::helper('adminhtml')->__('Store'),
                    'width' => 400,
                    'align' => 'left',
                    'index' => 'title',
                    'type' => 'options',
                    'options' => Mage::helper('ustorelocator')->getLocationOptions()
                ),
                'lastname'
            );
        }
    }

    /**
     * storeResolver
     *
     * resolve the store domain name and store the ip address in the store model
     *
     * @param Varien_Event_Observer $observer
     * @return
     */
    public function storeResolver(Varien_Event_Observer $observer) {
        $stores = Mage::getModel('ustorelocator/location')->getCollection();
        foreach($stores as $store) {
            $ip = null;
            try {
                $domainNamePostfix = Mage::getStoreConfig('ustorelocator/field/domain_name_postfix');
                $hostName = sprintf("%s%s", $store->getStoreCode(), $domainNamePostfix);
                $ip = gethostbyname($hostName);
                if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
                    $ip = null;
                    $authns = array();
                    $addtl = array();
                    $dns = dns_get_record($hostName, $type = DNS_ALL, $authns, $addtl, $raw = false);
                    if (is_array($dns) && count($dns) && is_array($dns[0]) && array_key_exists("ip", $dns[0])) {
                        $ip = $dns[0]["ip"];
                    }
                }
                Mage::helper('ustorelocator')->log(sprintf("%s->gethostbyname[%s]=%s", __METHOD__, $hostName, $ip));
            }
            catch(Exception $ex) {
                Mage::helper('ustorelocator')->log($ex->getMessage(), Zend_Log::ERR);
            }
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                $model = Mage::getModel('ustorelocator/location')->load($store->getId());
                Mage::helper('ustorelocator')->log(sprintf("%s->save[%d]=%s", __METHOD__, $store->getId(), $ip)); 
                $model->setIpAddress($ip);
                $model->save();
            }
        }
    }
}
