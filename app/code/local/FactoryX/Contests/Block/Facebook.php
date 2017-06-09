<?php
/**
 *	That is the frontend lookbook block
 */
class FactoryX_Contests_Block_Facebook extends FactoryX_Contests_Block_Contest
{
    /**
     * @return string
     */
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 86400,
            'cache_tags'     => array(FactoryX_Contests_Model_Contest::CACHE_TAG),
            'cache_key'      => parent::makeCacheKey(get_class($this))
        ));
    }
}