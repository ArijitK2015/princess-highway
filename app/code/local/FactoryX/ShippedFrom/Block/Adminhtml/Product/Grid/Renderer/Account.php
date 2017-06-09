<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Product_Grid_Renderer_Account
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Product_Grid_Renderer_Account
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * Renderer for the action column
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $locationUrl = $this->helper('adminhtml')->getUrl(
            'adminhtml/shippedfromaccount/edit',
            array('id' => $row->getData($this->getColumn()->getIndex()))
        );
        $retVal = sprintf("<a href='%s'>%s</a>", $locationUrl, $this->_getAccountName($row));
        return $retVal;
    }

    /**
     *  Render for export
     * @param Varien_Object $row
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        return $this->_getAccountName($row);
    }

    /**
     * @param $row
     * @return
     */
    protected function _getAccountName($row)
    {
        $accountId = $row->getData($this->getColumn()->getIndex());
        $accountName = $accountId;
        if (is_numeric($accountId)) {
            /** @var FactoryX_ShippedFrom_Model_Resource_Account_Collection $collection */
            $collection = Mage::getResourceModel('shippedfrom/account_collection')
                ->addFieldToFilter('account_id', $accountId)
                ->addFieldToSelect('name')
                ->setPageSize(1)
                ->setCurPage(1);
            if ($collection->getSize()) {
                /** @var FactoryX_ShippedFrom_Model_Account $account */
                $account = $collection->getFirstItem();
                $accountName = $account->getName();
            }
        }

        return $accountName;
    }
}