<?php

/**
 * Class FactoryX_ModulesFeed_Block_Adminhtml_Modulefeed_Grid
 * This is the block representing the grid of reports
 */
class FactoryX_ModulesFeed_Block_Adminhtml_Modulesfeed_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *	Constructor the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('modulesfeedGrid');
        $this->setDefaultSort('code_pool','DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     *	Prepare the collection to display in the grid
     */
    protected function _prepareCollection()
    {
        // Create a collection
        $collection = Mage::getSingleton('modulesfeed/feed');

        // We set the collection of the grid
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     *	Prepare the columns of the grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header' => Mage::helper('modulesfeed')->__('Name'),
            'index' => 'name',
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_name'
        ));

        $this->addColumn('file_enable', array(
            'header' => Mage::helper('modulesfeed')->__('Active'),
            'index' => 'file_enable',
            'type' => 'options',
            'options' => array(
                "true" => Mage::helper('modulesfeed')->__('Yes'),
                "false" => Mage::helper('modulesfeed')->__('No')
            ),
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_fileEnable'
        ));

        $this->addColumn('code_pool', array(
            'header' => Mage::helper('modulesfeed')->__('Code Pool'),
            'index' => 'code_pool',
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_codePool'
        ));

        $this->addColumn('version', array(
            'header' => Mage::helper('modulesfeed')->__('Version'),
            'index' => 'version',
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_version'
        ));

        $this->addColumn('folder_path', array(
            'header' => Mage::helper('modulesfeed')->__('Folder Path'),
            'index' => 'folder_path',
        ));

        $this->addColumn('folder_path_exists', array(
            'header' => Mage::helper('modulesfeed')->__('Folder Path Exists'),
            'index' => 'folder_path_exists',
            'type' => 'options',
            'options' => array(
                "true" => Mage::helper('modulesfeed')->__('Yes'),
                "false" => Mage::helper('modulesfeed')->__('No')
            ),
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_folderPathExists'
        ));

        $this->addColumn('data_entry', array(
            'header' => Mage::helper('modulesfeed')->__('Data Entry'),
            'index' => 'data_entry',
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_dataEntry'
        ));

        $this->addColumn('data_version', array(
            'header' => Mage::helper('modulesfeed')->__('Data Version'),
            'index' => 'data_version',
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_dataVersion'
        ));

        $this->addColumn('output_enable', array(
            'header' => Mage::helper('modulesfeed')->__('Output Enable'),
            'index' => 'output_enable',
            'type' => 'options',
            'options' => array(
                "true" => Mage::helper('modulesfeed')->__('Yes'),
                "false" => Mage::helper('modulesfeed')->__('No')
            ),
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_outputEnable'
        ));

        $this->addColumn('config_file_path', array(
            'header' => Mage::helper('modulesfeed')->__('Config File Path'),
            'index' => 'config_file_path'
        ));

        $this->addColumn('config_file_exists', array(
            'header' => Mage::helper('modulesfeed')->__('Config File Exists ?'),
            'index' => 'config_file_exists',
            'type' => 'options',
            'options' => array(
                "true" => Mage::helper('modulesfeed')->__('Yes'),
                "false" => Mage::helper('modulesfeed')->__('No')
            ),
            'renderer' => 'modulesfeed/adminhtml_template_grid_renderer_configFileExists'
        ));

        return $this;
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addItem('skin_css','css/factoryx/modulesfeed/styles.css');
        $this->getLayout()->getBlock('head')->addItem('skin_js','js/factoryx/modulesfeed/script.js');
        return parent::_prepareLayout();
    }
}
