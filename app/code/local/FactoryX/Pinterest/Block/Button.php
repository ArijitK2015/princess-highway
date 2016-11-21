<?php

/**
 * Class FactoryX_Pinterest_Block_Button
 */
class FactoryX_Pinterest_Block_Button extends FactoryX_Pinterest_Block_Header{
    protected function _construct()
    {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime' => 10,
            'cache_tags'     => array(FactoryX_Pinterest_Block_Header::CACHE_TAG),
            'cache_key'      => parent::makeCacheKey(get_class($this))
        ));
    }
}