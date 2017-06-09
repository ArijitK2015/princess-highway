<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pp
 * Date: 30.01.13
 * Time: 20:42
 *
 */

class FactoryX_StoreLocator_Block_Map extends Mage_Core_Block_Template {
    const PAGE_VAR  = 'page';
    const LIMIT_VAR = 'limit';
    /**
     * @var FactoryX_StoreLocator_Model_Mysql4_Location_Collection
     */
    protected $_collection;
    /**
     * @var int
     */
    protected $_limit;

    /**
     * @var FactoryX_StoreLocator_Helper_Data
     */
    protected $_helper;

    /**
     * add google maps js + api key
     */
    protected function _prepareLayout() {
        $apiKey = Mage::getStoreConfig('ustorelocator/api/google_maps');
        //$callback = false;
        $callback = "initialize";
        
        // <script defer = script that will not run until after the page has loaded
        // <script async = lets the browser render the rest of your website while the Maps JavaScript API loads
        // When the API is ready, it will call the function specified using the callback parameter initMap
        $js = sprintf("<script defer async src=\"//maps.googleapis.com/maps/api/js?key=%s%s\"></script>",
            $apiKey, (($callback) ? sprintf("&callback=%s", $callback) : ""));

        //Mage::helper('ustorelocator')->log(sprintf('addJs: %s', $js));
        $this->getLayout()->getBlock('head')->append(
            $this->getLayout()->createBlock('core/text', 'google_maps_api')->setText($js)
        );        
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getRegions(){
        $regions = Mage::getModel('ustorelocator/location')->getCollection()->addFieldToSelect(array('region'))->addFieldToFilter('is_featured', 1);
        $regions->getSelect()->group('region');
        $result = array();
        foreach ($regions as $region){
            $result[] = $region['region'];
        }
        return $result;
    }

    /**
     * @deprecated since 2.2.0
     * @return array
     */
    public function getStates()
    {
        return $this->getRegions();
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getPager();

        if ($pagerBlock instanceof Varien_Object && $this->getCollection()) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setLimit($this->getLimit())
                ->setLimitVarName(self::LIMIT_VAR)
                ->setPageVarName(self::PAGE_VAR)
                ->setShowPerPage(false)
                ->setFrameLength(3)
                ->setJump(3)
                ->setCollection($this->getCollection())
                ->setData('show_amounts', false)
                ->setData('use_container', false);

            return $pagerBlock->toHtml();
        }

        return '';
    }

    /**
     * @return Mage_Page_Block_Html_Pager
     */
    public function getPager()
    {
        $pagerBlock = $this->getChild('ustorelocator.locations.pager');

        return $pagerBlock;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        if (!isset($this->_limit)) {
            $this->_limit = (int)Mage::getStoreConfig('ustorelocator/general/num_results');
        }

        return $this->_limit;
    }

    /**
     * @return FactoryX_StoreLocator_Model_Mysql4_Location_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * @param Varien_Data_Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        if (!$collection instanceof Varien_Data_Collection) {
            Mage::helper('ustorelocator')->log(sprintf('Wrong collection passed: %s', get_class($collection)), Zend_Log::ERR);
            return $this;
        }
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getCurrentOrder()
    {
        return false; // todo
    }

    /**
     * @return string
     */
    public function getCurrentDirection()
    {
        return 'ASC'; // todo
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        if ($page = (int)$this->getRequest()->getParam(self::PAGE_VAR)) {
            return $page;
        }

        return 1;
    }

    /**
     * @return FactoryX_StoreLocator_Helper_Data
     */
    public function getSlHelper()
    {
        if (!isset($this->_helper)) {
            $this->_helper = $this->helper('ustorelocator');
        }

        return $this->_helper;
    }
}