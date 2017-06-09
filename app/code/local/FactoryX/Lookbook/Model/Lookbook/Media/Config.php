<?php
/**
 * Based on /app/code/core/Mage/Catalog/Model/Product/Media/Config.php
 */
class FactoryX_Lookbook_Model_Lookbook_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    /**
     * Filesystem directory path of lookbook images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaPathAddition()
    {
        return 'lookbook';
    }

    /**
     * Web-based directory path of lookbook images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaUrlAddition()
    {
        return 'lookbook';
    }

    /**
     * Filesystem directory path of temporary lookbook images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaPathAddition()
    {
        return 'tmp' . DS . $this->getBaseMediaPathAddition();
    }

    /**
     * Web-based directory path of temporary lookbook images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaUrlAddition()
    {
        return 'tmp/' . $this->getBaseMediaUrlAddition();
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return Mage::getBaseDir('media') . DS . 'lookbook';
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return Mage::getBaseUrl('media') . 'lookbook';
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return Mage::getBaseDir('media') . DS . $this->getBaseTmpMediaPathAddition();
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        return Mage::getBaseUrl('media') . $this->getBaseTmpMediaUrlAddition();
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if(substr($file, 0, 1) == '/') {
            return $this->getBaseMediaUrl() . $file;
        }

        return $this->getBaseMediaUrl() . '/' . $file;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaPath($file)
    {
        $file = $this->_prepareFileForPath($file);

        if(substr($file, 0, 1) == DS) {
            return $this->getBaseMediaPath() . DS . substr($file, 1);
        }

        return $this->getBaseMediaPath() . DS . $file;
    }

    /**
     * @param $file
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if(substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseTmpMediaUrl() . '/' . $file;
    }

    /**
     * Part of URL of temporary lookbook images
     * relatively to media folder
     *
     * @param $file
     * @return string
     */
    public function getTmpMediaShortUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if(substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseTmpMediaUrlAddition() . '/' . $file;
    }

    /**
     * Part of URL of lookbook images relatively to media folder
     *
     * @param $file
     * @return string
     */
    public function getMediaShortUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if(substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseMediaUrlAddition() . '/' . $file;
    }

    /**
     * @param $file
     * @return string
     */
    public function getTmpMediaPath($file)
    {
        $file = $this->_prepareFileForPath($file);

        if(substr($file, 0, 1) == DS) {
            return $this->getBaseTmpMediaPath() . DS . substr($file, 1);
        }

        return $this->getBaseTmpMediaPath() . DS . $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function _prepareFileForUrl($file)
    {
        return str_replace(DS, '/', $file);
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function _prepareFileForPath($file)
    {
        return str_replace('/', DS, $file);
    }
}