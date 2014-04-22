<?php
/**
 * Based on /app/code/core/Mage/Catalog/Helper/Image.php
 */
class FactoryX_Lookbook_Helper_Image extends Mage_Core_Helper_Abstract
{
	protected $_model;
    protected $_lookbook;
	protected $_imageFile;
	protected $_scheduleResize = false;
	protected $_scheduleRotate = false;

    /**
     * Reset all previos data
     */
    protected function _reset()
    {
        $this->_lookbook = null;
        $this->_model = null;
        $this->_imageFile = null;
        $this->_scheduleResize = false;
        $this->_scheduleRotate = false;
        return $this;
    }

    public function init(FactoryX_Lookbook_Model_Lookbook $lookbook, $attributeName, $imageFile=null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('lookbook/lookbook_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setLookbook($lookbook);

        if ($imageFile) {
            $this->setImageFile($imageFile);
        }
        else {
            // add for work original size
            $this->_getModel()->setBaseFile( $this->getLookbook()->getData($this->_getModel()->getDestinationSubdir()) );
        }
        return $this;
    }

    public function __toString()
    {
        try {
            if( $this->getImageFile() ) {
                $this->_getModel()->setBaseFile( $this->getImageFile() );
            } else {
                $this->_getModel()->setBaseFile( $this->getLookbook()->getData($this->_getModel()->getDestinationSubdir()) );
            }

            if( $this->_getModel()->isCached() ) {
                return $this->_getModel()->getUrl();
            } else {
                if( $this->_scheduleRotate ) {
                    $this->_getModel()->rotate( $this->getAngle() );
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                $url = $this->_getModel()->saveFile()->getUrl();
            }
        } catch( Exception $e ) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }
	
	/**
     * Set current Image model
     *
     * @param Mage_Catalog_Model_Product_Image $model
     * @return Mage_Catalog_Helper_Image
     */
    protected function _setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Get current Image model
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    protected function _getModel()
    {
        return $this->_model;
    }

    protected function setLookbook($lookbook)
    {
        $this->_lookbook = $lookbook;
        return $this;
    }

    protected function getLookbook()
    {
        return $this->_lookbook;
    }
	
	/**
     * Set Image file
     *
     * @param string $file
     * @return Mage_Catalog_Helper_Image
     */
    protected function setImageFile($file)
    {
        $this->_imageFile = $file;
        return $this;
    }
	
	protected function getImageFile()
    {
        return $this->_imageFile;
    }

    protected function setProduct($product)
    {
        Mage::throwException($this->__('This helper only accepts myobject objects, use setLookbook().'));
    }

    protected function getProduct()
    {
        Mage::throwException($this->__('This helper only accepts myobject objects, use getLookbook().'));
    }
	
	/**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be calculated.
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param int $width
     * @param int $height
     * @return Mage_Catalog_Helper_Image
     */
    public function resize($width, $height = null)
    {
        $this->_getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleResize = true;
        return $this;
    }

}