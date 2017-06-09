<?php 

class Unirgy_StoreLocator_Helper_Protected
{
    private static $_licenseIsValid = array();

    const SEARCH_TAG = "search_tag";
    const COUNTRY = "country";

    final public static function validateLicense($module = "Unirgy_StoreLocator")
    {
        if( !empty($_licenseIsValid[$module]) || in_array($_SERVER["REMOTE_ADDR"], array( "192.168.56.1" )) ) 
        {
            return true;
        }

        if( Mage::getconfig()->getNode("modules/Unirgy_SimpleLicense") ) 
        {
            $key = "VN29643YBOFNSD86R2VOEWYEIF" . microtime(true);
            Unirgy_SimpleLicense_Helper_Protected::obfuscate($key);
            $hash = Unirgy_SimpleLicense_Helper_Protected::validatemodulelicense($module);
            if( sha1($key . $module) !== $hash ) 
            {
                Mage::throwexception("Invalid response from validation method");
            }

            $_licenseIsValid[$module] = true;
            return true;
        }

        if( !ioncube_license_matches_server() ) 
        {
            Mage::throwexception("Invalid ionCube license for Unirgy_StoreLocator. Allowed servers: " . ioncube_licensed_servers());
        }

        if( ioncube_license_has_expired() ) 
        {
            Mage::throwexception("ionCube license for Unirgy_StoreLocator has expired");
        }

        $_licenseIsValid[$module] = true;
        return true;
    }

    public function getCollection($request)
    {
        $collection = Mage::getmodel("ustorelocator/location")->getCollection();
        $lat = $request["lat"];
        $lng = $request["lng"];
        $radius = $request["radius"];
        $units = $request["units"];
        if( $request["address"] ) 
        {
            $address = trim($request["address"]);
            $address = "%" . str_replace(" ", "%", $address) . "%";
            $collection->addFieldToFilter("title", array( "like" => $address ));
        }

        $sort = (Mage::getstoreconfig("ustorelocator/general/default_sort") == Unirgy_StoreLocator_Model_Settings_Sort::ALPHA ? "title" : "distance");
        $loc_type = $request["loc_type"];
        $tag = $this->getSearchTag($request);
        if( $loc_type == self::COUNTRY ) 
        {
            $country = $request["short_name"];
            $collection->addCountryFilter($lat, $lng, $country, $units);
        }
        else
        {
            $collection->addAreaFilter($lat, $lng, $radius, $units);
        }

        if( $tag ) 
        {
            $collection->addProductTypeFilter($tag);
        }

        $collection->addStoreFilter()->addOrder("is_featured", Zend_Db_Select::SQL_DESC)->addOrder($sort, Zend_Db_Select::SQL_ASC);
        return $collection;
    }

    protected function getSearchTag($req)
    {
        $session = Mage::getsingleton("customer/session");
        if( isset($req["tag"]) ) 
        {
            $session->setData(self::SEARCH_TAG, $req["tag"]);
        }

        return $session->getData(self::SEARCH_TAG);
    }

    protected function clearSearchTag()
    {
        $session = Mage::getsingleton("customer/session");
        $session->unsetData(self::SEARCH_TAG);
    }

    protected function getSearchRequest($req)
    {
        $units = Mage::getstoreconfig("ustorelocator/general/distance_units");
        $result = array( $req["lat"], $req["lng"], $req["radius"], (isset($req["units"]) ? $req["units"] : $units) );
        return $result;
    }

    public function validateIconSize($filePath)
    {
        $info = getimagesize($filePath);
        if( !$info ) 
        {
            throw new Exception("Uploaded file is not an image");
        }

        $width = $info[0];
        $height = $info[1];
        if( 100 < $width || 100 < $height ) 
        {
            throw new Exception("Uploaded image exceeds 100px width or height");
        }

        if( $info[2] != IMAGETYPE_PNG ) 
        {
            throw new Exception("Allowed icons of type PNG only.");
        }

    }

}


