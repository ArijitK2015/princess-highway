<?php
/**
 * Pdf creation engine source model.
 *
 * @category  FireGento
 * @package   FireGento_Pdf
 * @author    FireGento Team <team@firegento.com>
 */
class FireGento_Pdf_Model_System_Config_Source_Order_Engine
{
    /**
     * Config xpath to pdf engine node
     *
     */
    const XML_PATH_PDF_ENGINE = 'global/pdf/firegento_invoice_engines';

    /**
     * Return array of possible engines.
     *
     * @return array
     */
    public function toOptionArray()
    {
        // load default engines shipped with Mage_Sales and FireGento_Pdf
        $engines = array(
            ''                                     => Mage::helper('firegento_pdf')->__('Standard Magento'),
            'firegento_pdf/engine_invoice_default' => Mage::helper('firegento_pdf')->__('Standard FireGento')
        );

        // load additional engines provided by third party extensions
        $engineNodes = Mage::app()->getConfig()->getNode(self::XML_PATH_PDF_ENGINE);
        if ($engineNodes && $engineNodes->hasChildren()) {
            foreach ($engineNodes->children() as $engineName => $engineNode) {
                $className   = (string) $engineNode->class;
                $engineLabel = Mage::helper('firegento_pdf')->__((string) $engineNode->label);
                $engines[$className] = $engineLabel;
            }
        }

        $options = array();
        foreach ($engines as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }
        return $options;
    }
}
