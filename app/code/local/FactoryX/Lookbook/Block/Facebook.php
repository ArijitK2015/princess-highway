<?php
/**
 *	That is the frontend lookbook block
 */
class FactoryX_Lookbook_Block_Facebook extends FactoryX_Lookbook_Block_Lookbook
{
    /**
     * @return string
     */
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 86400,
            'cache_tags'     => array(FactoryX_Lookbook_Model_Lookbook::CACHE_TAG),
            'cache_key'      => parent::makeCacheKey(get_class($this))
        ));
    }
}