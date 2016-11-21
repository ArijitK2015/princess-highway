<?php

/**
 * Class FactoryX_CustomReports_Model_Report
 */
class FactoryX_CustomReports_Model_Report extends Varien_Data_Collection {

    /**
     * @param bool $printQuery
     * @param bool $logQuery
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        // We add the renders
        $this->_renderFilters()
            ->_renderOrders()
            ->_renderLimit();

        $this->_setIsLoaded();
    }
    
    /**
     * This method is called by Magento when you filter
     * Below is the most classical type of condition, a string with a LIKE search like SQL
     * @param $field field to filter
     * @param null $condition : Array ( [like] => Zend_Db_Expr Object ( [_expression:protected] => '%USER STRING%' ) )
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $keyFilter = key($condition);
        $valueFilter = (string)$condition[$keyFilter];
        $this->addFilter($field, $valueFilter);
        return $this;
    }

    /**
     * Render filter method
     * @return $this
     */
    protected function _renderFilters()
    {
        // If elements are already filtered, return this
        if ($this->_isFiltersRendered) {
            return $this;
        }

        // Loop the collection
        foreach($this->_items AS $key => $item) {

            foreach($this->_filters AS $filter) {

                $keyFilter = $filter->getData()['field'];
                $valueFilter = $filter->getData()['value'];
                //$condFilter = $filter->getData()['type']; // not used in this example
                if (strpos($valueFilter, "%") !== false) {
                    // Delete "'%" and "%'" from the string
                    $valueFilter = substr($filter->getData()['value'], 2, -2);
                }

                // Item value
                $itemValue = $item->getData($keyFilter);

                // If it's not an array, we use the search term to compare with the value of our item
                if (!is_array($itemValue)) {
                    if (strpos(strtolower($itemValue), strtolower($valueFilter)) === FALSE) {
                        unset($this->_items[$key]);
                        // If search term not found, unset the item to not display it!
                    }
                } else {
                    // If it's an array
                    $found = false;
                    foreach ($itemValue AS $value) {
                        if (strpos(strtolower($value), strtolower($valueFilter)) !== FALSE) {
                            $found = true;
                        }
                    }
                    if (!$found) {
                        unset($this->_items[$key]); // Not founded in the array, so unset the item
                    }
                }
            }
        }

        $this->_isFiltersRendered = true;
        return $this;
    }

    /**
     * Render the order
     * @return $this
     */
    protected function _renderOrders()
    {
        $keySort = key($this->_orders);
        if ($keySort && array_key_exists($keySort, $this->_orders)) {
            $keyDirection = $this->_orders[$keySort];
            //Mage::helper('customreports')->log(sprintf("%s->keySort: %s|%s", __METHOD__, $keySort, $keyDirection));            
            // We sort our items tab with a custom function AT THE BOTTOM OF THIS CODE
            usort($this->_items, $this->_build_sorter($keySort, $keyDirection));
        }
        return $this;
    }

    /**
     * Render the limit
     * @return $this
     */
    protected function _renderLimit()
    {
        $this->_totalRecords = sizeof($this->_items);

        if($this->_pageSize){
            $this->_items = array_slice($this->_items, ($this->_curPage - 1) * $this->_pageSize, $this->_pageSize);
        }
        return $this;
    }

    /**
     * Custom function to sort
     * @param $key
     * @param $direction
     * @return Closure
     */
    protected function _build_sorter($key,$direction)
    {
        return function ($a, $b) use ($key,$direction) {
            if ($direction == self::SORT_ORDER_ASC)
                return strnatcmp($a[$key], $b[$key]); // Natural comparaison of string
            else
                return -1 * strnatcmp($a[$key], $b[$key]); // reverse the result if sort order desc !
        };
    }

}