<?php

class FactoryX_ReportsViewer_Model_Reports_Collection extends Varien_Data_Collection_Filesystem
{
    /**
     * Folder, where all reports are stored
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Set collection specific parameters and make sure report folder will exist
     */
    public function __construct()
    {
        parent::__construct();

        $this->_baseDir = Mage::getBaseDir('var') . DS . 'report';

        $this
            ->setOrder('time', self::SORT_ORDER_DESC)
            ->addTargetDir($this->_baseDir)
            ->setFilesFilter('/^[0-9]+$/')
            ->setCollectRecursively(false)
        ;
    }

    /**
     * Get backup-specific data from model for each row
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        foreach (Mage::getSingleton('reportsviewer/report')->load($row['basename'], $this->_baseDir)
                     ->getData() as $key => $value) {
            $row[$key] = $value;
        }
        $row['id'] = $row['report_id'];
        return $row;
    }
}