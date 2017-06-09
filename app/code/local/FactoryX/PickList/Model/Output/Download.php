<?php
/**
output download
*/

class FactoryX_PickList_Model_Output_Download {

    const DIR = "picklists";
    protected $_helper;

    protected $files = array();
    protected $params = array();
    
    /**
    */    
    public function getFiles() {
        return $this->files;
    }

    /**
     * @param $files
     */
    public function setFiles($files) {
        $this->files = $files;
    }

    /**
    */    
    public function getParams() {
        return $this->params;
    }

    /**
     * @param $params
     */
    public function setParams($params) {
        $this->params = $params;
    }


    /**
     * Model initialization
     *
     */
    protected function _construct() {
        Mage::log(sprintf("%s->var=%s", __METHOD__, get_class($this)) );

        //$this->_init('picklist/output_download');
        $this->_helper = Mage::helper('picklist');
    }

    /**
    delete everything in the output dir
    TODO: return an array, which can be rendered
    */
    public function purgeOutputDir() {
        $outputPath = $this->getOutputDir();

        // double check
        //Mage::helper('picklist')->log(sprintf("%s == %s", $outputPath, Mage::getBaseDir()));
        //Mage::helper('picklist')->log(sprintf("%s == %s", $outputPath, Mage::getBaseDir('media')));
        
        if ($outputPath == Mage::getBaseDir() || $outputPath == Mage::getBaseDir('media')) {
            throw new Exception(sprintf("%s->reject attempt to delete '%s'!", __METHOD__, $outputPath));
        }
        
        $files = glob(sprintf("%s/*", $outputPath)); // get all file
        $jobLog = "";
        foreach($files as $file) {
            try {
                $msg = sprintf("delete %s", $file);
                Mage::helper('picklist')->log($msg);
                $jobLog .= sprintf("%s\n", $msg);
                self::deleteFiles($file);
            }
            catch(Exception $ex) {
                $err = sprintf("failed to delete '%s', error: %s", $file, $ex->getMessage());
                Mage::helper('picklist')->log($err);
                $jobLog .= sprintf("%s\n", $err);
            }
            /*
            if (is_file($file)) {
                unlink($file); // delete file
            }
            */
        }
        return $jobLog;
    }

    /**
     * php delete function that deals with directories recursively
     * @param $target
     */
    private static function deleteFiles($target) {
        if (is_dir($target)) {
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
            foreach( $files as $file ) {
                self::deleteFiles($file);
            }
            rmdir($target);
        }
        else if(is_file($target)) {
            unlink($target);
        }
    }

    /**
     * @throws Exception
     * @return string $patrh path to out put directory
     */
    public function getOutputDir() {

        // try from config first
        $useConfig = false;
        if (Mage::helper('picklist')->getDownloadOutputPath()) {
            $path = Mage::helper('picklist')->getDownloadOutputPath();
            $picklists = sprintf("%s%s", Mage::getBaseDir(), $path);
            if (!is_dir($picklists)) {
                // try and make it
                if (mkdir($picklists)) {
                    $useConfig = true;
                }
            }
            else {
                $useConfig = true;
            }
        }
        // otherwise media/picklists
        if (!$useConfig) {
            $media = Mage::getBaseDir('media');
            if (!is_dir($media)) {
                throw new Exception(sprintf("cannot find directory '%s'", $media));
            }

            $picklists = sprintf("%s/%s", $media, self::DIR);
            if (!is_dir($picklists)) {
                if (!mkdir($picklists)) {
                    throw new Exception(sprintf("cannot create directory '%s': %s", $picklists, error_get_last()));
                }
            }
        }
        return $picklists;
    }

    /**
     * copy tmp files to media
     * @param $path
     * @param $outFiles
     * @param $labels
     * @param $label
     * @throws Exception
     * @throws Mage_Core_Exception
     * @return array
     */
    public function copyFilesToMedia($path, $outFiles, $labels, $label) {

        $files = array();
        foreach ($outFiles as $file) {
            // $parts['basename'];
            $parts = pathinfo($file);
            // one store
            if (isset($labels) && is_array($labels)) {
                $label = array_pop($labels);
            }
            $ts = Mage::getModel('core/date')->timestamp(time());
            $newName = sprintf("%s_%s_pick_list.pdf", date('Ymd_a', $ts), $label);
            $new = sprintf("%s/%s", $path, $newName);
            if (!rename($file, $new)) {
                throw new Exception(sprintf("file rename from '%s' to '%s' failed: %s", $file, $new, error_get_last()));
            }
            if (!chmod($new, 0644)) {
                throw new Exception(sprintf("cannot chmod '%s' to '0644': %s", $new, error_get_last()));
            }
            $new = str_replace(Mage::getBaseDir('media'), '', $new);
            if (preg_match("/^\//", $new)) {
                $new = substr($new, 1);
            }

            // Mage_Core_Model_Store::URL_TYPE_LINK
            $storeId = 0;
            $mediaPath = Mage::app()->getStore($storeId)->getBaseUrl('media', array('_secure' => false));
            $files[] = array(
                'url'   => sprintf("%s%s", $mediaPath, $new),
                'store' => $label,
                'file'  => $new,
                'name'  => $newName
            );
        }
        return $files;
    }

    /**
     * @param null $path
     * @throws Exception
     * @return null|string
     */
    public function createTmpDir($path = null) {
        if (!$path) {
            $path = $this->getOutputDir();
        }

        $dir = hash("md5", time());
        $path = sprintf("%s/%s", $path, $dir);

        //$this->_helper->log(sprintf("mkdir '%s'", $path));
        if (!mkdir($path)) {
            throw new Exception(sprintf("cannot create directory '%s': %s", $path, error_get_last()));
        }

        return $path;
    }

    /**
     * @param $link
     * @return string
     */
    public function createUrlHref($link) {
        $html = sprintf("<a href='%s'>%s</a>", $link, $link);
        return $html;

    }

}
