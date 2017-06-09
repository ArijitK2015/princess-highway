<?php

/**
 * Class FactoryX_ReportsViewer_Helper_Data
 */
class FactoryX_ReportsViewer_Helper_Data extends Mage_Core_Helper_Abstract
{
    // Protected log file name
    protected $_logFileName = 'factoryx_reportsviewer.log';

    /**
     * Log data to a custom file
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->_logFileName);
    }

    /**
     * Extracts information from report's filepath
     *
     * @param string $filepath
     * @return Varien_Object
     */
    public function extractDataFromFile($filepath)
    {
        // Read the unserialize content of the file
        $content = unserialize(file_get_contents($filepath));

        // Loop through the array
        foreach ($content as $key => $value)
        {
            // Value with key = 0 is always the error message
            if (!$key)
            {
                $error = $value;
            }
            elseif ($key == "url")
            {
                $url = $value;
            }
            elseif ($key == "script_name")
            {
                $script_name = $value;
            }
            elseif ($key == "skin")
            {
                $skin = $value;
            }
            else
            {
                // The trace has the key = 1, we do it last
                $trace = $value;
            }
        }

        // Create the result object
        $result = new Varien_Object();
        $result->addData(array(
            'error' => $error ? $error : "",
            'url' => $url ? $url : "",
            'script_name' => $script_name ? $script_name : "",
            'skin' => $skin ? $skin : "",
            'trace' => $trace ? $trace : "",
            'time' => filemtime($filepath)
        ));

        return $result;
    }

}