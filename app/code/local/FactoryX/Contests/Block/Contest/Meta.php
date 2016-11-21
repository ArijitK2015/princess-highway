<?php

/**
 * Class FactoryX_Contests_Block_Contest_Meta
 */
class FactoryX_Contests_Block_Contest_Meta
    extends Mage_Core_Block_Template {

    /**
     * @return bool
     * @throws Exception
     */
    public function getContest()
    {
        try {
            if ($id = $this->getRequest()->getParam('id')) {
                $collection = Mage::getResourceModel('contests/contest_collection')
                    ->addFieldToSelect(array('title', 'image_url'))
                    ->addFieldToFilter('contest_id', $id)
                    ->setPageSize(1)
                    ->setCurPage(1);

                if ($collection->getSize()) {
                    return $collection->getFirstItem();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            Mage::helper('contests')->log($e->getMessage());
            return false;
        }
    }
}