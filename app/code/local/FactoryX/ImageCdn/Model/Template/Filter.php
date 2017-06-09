<?php

/**
 * Template Filter Model
 *
 * @category    ImageCdn
 * @package     FactoryX_ImageCdn
 * @author      Haibin Li
 */
class FactoryX_ImageCdn_Model_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    /**
     *  CDN media URL filter
     * @param array $construction
     * @return string
     */
    public function imagecdnDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);

        if (empty($params['type'])) return '';
        $type = $params['type'];

        switch ($type) {
            case "prod":
                // Determine what name block should have in layout
                $width = isset($params['width'])? $params['width']:null;
                $height = isset($params['height'])? $params['height']:null;

                // validate required parameter type or id
                if (empty($params['id'])) return '';
                $id = $params['id'];
                $_product = Mage::getModel('catalog/product');
                $_product->load($id);
                $_small_image_file = $_product->getSmall_image();
                $img = Mage::helper('catalog/image')->init($_product, 'small_image', $_small_image_file);

                return $img->resize($width, $height)->__toString();
                break;
            default:
                return '';
        }

    }
}
