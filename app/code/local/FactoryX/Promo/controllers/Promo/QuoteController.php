<?php
/**
 */

/**
 * Adminhtml promo cataloge controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

include_once("Mage/Adminhtml/controllers/Promo/QuoteController.php");

/**
 * Class FactoryX_Promo_Promo_QuoteController
 */
class FactoryX_Promo_Promo_QuoteController extends Mage_Adminhtml_Promo_QuoteController {
    
    public function massDeleteAction()
    {
        $rule_ids = $this->getRequest()->getParam('rule_ids');
        if(!is_array($rule_ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select rule(s).'));
        } else {
            try {
                $rule = Mage::getModel('salesrule/rule');
                foreach ($rule_ids as $rule_id) {
                    $rule->load($rule_id)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($rule_ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
       $fileName   = 'coupon.csv';
       $grid       = $this->getLayout()->createBlock('adminhtml/promo_quote_grid');
       $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/promo_quote_grid')->toHtml()
        ); 
    }

}