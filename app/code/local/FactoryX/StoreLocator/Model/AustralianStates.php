<?php

/**
 * Class FactoryX_StoreLocator_Model_AustralianStates
 */
class FactoryX_StoreLocator_Model_AustralianStates
{
    protected static $_australianStates = array(
        "act"   =>  "Australian Capital Territory",
        "nsw"   =>  "New South Wales",
        "nt"    =>  "Northern Territory",
        "qld"   =>  "Queensland",
        "sa"    =>  "South Australia",
        "tas"   =>  "Tasmania",
        "vic"   =>  "Victoria",
        "wa"    =>  "Western Australia",
        "nz"    =>  "New Zealand"
    );

    /**
     * @param $longCode
     * @return mixed
     */
    static public function getShortCode($longCode)
    {
        return array_search($longCode, self::$_australianStates);
    }

    /**
     * @param $shortCode
     * @return bool|mixed
     */
    static public function getLongCode($shortCode)
    {
        return (array_key_exists($shortCode, self::$_australianStates) ? self::$_australianStates[$shortCode] : FALSE);
    }

    /**
     * @return array
     */
    static public function getAustralianStates()
    {
        return self::$_australianStates;
    }
}