<?php

class FactoryX_CartImage_Block_AW_Ajaxcartpro_Confirmation_Items_Productimage extends AW_Ajaxcartpro_Block_Confirmation_Items_Productimage {

    protected function _toHtml()
    {
        if (!Mage::helper('cartimage')->isEnabled()) {
            return parent::_toHtml();
        } else {
            $simpleProduct = $this->getData('product');
            if ($simpleProduct) {
                $product = $this->getProduct();
                $customAttribute = $this->hlp()->getCustomAttribute();
                $loadedAttribute = Mage::getResourceModel('catalog/product')->getAttribute($customAttribute);
                $chosenLabel = $this->getChosenLabel($simpleProduct, $customAttribute, $loadedAttribute);

                $matchingThumbnail = $this->hlp()->getMatchingThumbnail($product, $this->generateOptionsArray($loadedAttribute, $chosenLabel));

                $resize = $this->getResize();
                if (is_null($resize)) {
                    $resize = 265;
                }

                $label = $product->getData('small_image_label');
                if (empty($label)) {
                    $label = $product->getName();
                }

                $img = '<img src="' . $matchingThumbnail->resize($resize) .
                    '" alt="' . $this->escapeHtml($label) .
                    '" title="' . $this->escapeHtml($label) .
                    '" width="' . $resize .
                    '" height="' . $resize .
                    '" />';

                return $img;
            } else {
                return parent::_toHtml();
            }
        }
    }

    /**
     * @return FactoryX_CartImage_Helper_Data
     */
    protected function hlp()
    {
        return Mage::helper('cartimage');
    }

    /**
     * @param $simpleProduct
     * @param $customAttribute
     * @param $loadedAttribute
     * @return mixed
     */
    protected function getChosenLabel($simpleProduct, $customAttribute, $loadedAttribute)
    {
        $chosenColor = $simpleProduct->getData($customAttribute);
        $chosenLabel = $loadedAttribute
            ->getSource()
            ->getOptionText($chosenColor);
        return $chosenLabel;
    }

    /**
     * @param $loadedAttribute
     * @param $chosenLabel
     * @return array
     */
    protected function generateOptionsArray($loadedAttribute, $chosenLabel): array
    {
        return [['label' => $loadedAttribute->getFrontendLabel(), 'value' => $chosenLabel]];
    }
}