<?php
/**
 * Auth session model
 *
 * @category   Mage
 * @package    FactoryX_XMLFeed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FactoryX_XMLFeed_Model_Xmlfeed
{
    protected $_feedArray = array();

    /**
     * @param array $data
     * @return $this
     */
    public function _addHeader($data = array())
    {
        $this->_feedArray = $data;
        return $this;
    }

    /**
     * @param $entries
     * @return $this
     */
    public function _addEntries($entries)
    {
        $this->_feedArray['entries'] = $entries;
        return $this;
    }

    /**
     * @param $entry
     * @return $this
     */
    public function _addEntry($entry)
    {
        $this->_feedArray['entries'][] = $entry;
        return $this;
    }

    /**
     * @return array
     */
    public function getFeedArray()
    {
        return $this->_feedArray;
    }

    /**
     * @return string
     */
    public function createXMLFeed() {
        try {
            $xmlFeedFromArray = Zend_Feed::importArray($this->getFeedArray(), 'xmlfeed');
            return $xmlFeedFromArray->saveXML();
        } catch (Exception $e) {
            return Mage::helper('xmlfeed')->__('Error in processing xml. %s',$e->getMessage());
        }
    }
}
