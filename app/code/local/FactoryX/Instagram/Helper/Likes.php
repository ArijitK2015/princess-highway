<?php
class FactoryX_Instagram_Helper_Likes extends Mage_Core_Helper_Abstract
{
    /**
     * @param $value
     * @return string
     */
    public function getEndpointUrl($value)
    {
        // end point with no token???
        return 'https://api.instagram.com/v1/media/' . ltrim($value, '#') . '?client_id=' . $this->getClientId();
    }

    /**
     * @return mixed
     */
    protected function getClientId()
    {
        return Mage::getStoreConfig('factoryx_instagram/module_options/client_id');
    }

    /**
     * @param $mediaId
     * @return mixed
     */
    public function getLikes($mediaId){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getEndpointUrl($mediaId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if (Mage::getStoreConfig('factoryx_instagram/module_options/proxy')){
            Mage::helper('instagram')->log(sprintf("%s->proxy=%s", __METHOD__, Mage::getStoreConfig('factoryx_instagram/module_options/proxy')), Zend_Log::DEBUG );
            curl_setopt($ch, CURLOPT_PROXY, Mage::getStoreConfig('factoryx_instagram/module_options/proxy'));
        }
        $output = curl_exec($ch);
        // echo curl_error($ch);
        curl_close($ch);
        $data = json_decode($output);
        unset($output);
        $count = $data->data->likes->count;
        return $count;
    }
}
