<?php
/**
 *	Fix _addColumnFilterToCollection to search for null
 */
class FactoryX_BugFixes_Block_Adminhtml_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            }
            else {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {                	 
                	// Treat NULLS differently
                	if (in_array('NULL', array_values($cond), true)) {
                		$this->getCollection()->addFieldToFilter($field, array('null' => true));
                    }
                    else {
                    	$this->getCollection()->addFieldToFilter($field, $cond);
                	}
                }
            }
        }
        return $this;
    }
}
