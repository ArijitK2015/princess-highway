<?php
/**
 * Based on /app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Gallery/Content.php
 *
 * Note: for translations, the catalog helper is used.
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Media_Gallery_Content extends Mage_Adminhtml_Block_Widget
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/lookbook/media/gallery.phtml');
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('uploader',
            $this->getLayout()->createBlock('adminhtml/media_uploader')
        );

        if (class_exists("Mage_Uploader_Block_Abstract")) {
            // PATCH SUPEE-8788 or Magento 1.9.3
            $this->getUploader()->getUploaderConfig()
                ->setFileParameterName('image')
                ->setTarget(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/upload'));

            $browseConfig = $this->getUploader()->getButtonConfig();
            $browseConfig
                ->setAttributes(
                    array("accept"  =>  $browseConfig->getMimeTypesByExtensions('gif, png, jpeg, jpg'))
                );
        } else {
            $this->getUploader()->getConfig()
                ->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/upload'))
                ->setFileField('image')
                ->setFilters(array(
                    'images' => array(
                        'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                        'files' => array('*.gif', '*.jpg','*.jpeg', '*.png')
                    )
                ));
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrive uploader block
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
     */
    public function getUploader()
    {
        return $this->getChild('uploader');
    }

    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * @return string
     */
    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            Mage::helper('catalog')->__('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    /**
     * @return string
     */
    public function getImagesJson()
    {
        if(is_array($this->getElement()->getDataObject()->getGallery())) {

            $gallery = $this->getElement()->getDataObject()->getGallery();
            if(count($gallery)>0) {
                foreach ($gallery as &$image) {
                    $image['url'] = Mage::getSingleton('lookbook/lookbook_media_config')
                        ->getMediaUrl($image['file']);
                }
                return Mage::helper('core')->jsonEncode($gallery);
            }
        }

        return '[]';
    }

    /**
     * @return mixed
     */
    public function getImagesValuesJson()
    {
        $values = array();
        foreach ($this->getMediaAttributes() as $code => $label) {
            $values[$code] = $this->getElement()->getDataObject()->getData($code);
        }
        return Mage::helper('core')->jsonEncode($values);
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getImageTypes()
    {
        $imageTypes = array();
        foreach ($this->getMediaAttributes() as $code => $label) {
            $imageTypes[$code] = array(
                'label' => Mage::helper('lookbook')->__($label),
                'field' => $this->getElement()->getAttributeFieldName(array($code => $label)),
            );
        }
        return $imageTypes;
    }

    /**
     * Returns all media attributes available for the images.
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        return $this->getElement()->getDataObject()->getMediaAttributes();
    }

    /**
     * @return mixed
     */
    public function getImageTypesJson()
    {
        return Mage::helper('core')->jsonEncode($this->getImageTypes());
    }

}