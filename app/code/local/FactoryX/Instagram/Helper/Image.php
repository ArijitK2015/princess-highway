<?php
/**
 * iKantam
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade InstagramConnect to newer
 * versions in the future.
 *
 * @category    Ikantam
 * @package     FactoryX_Instagram
 * @copyright   Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FactoryX_Instagram_Helper_Image extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     * @deprecated since 1.6.0
     */
    const MAX_PHOTO_COUNT = 50;

    /**
     * @var string
     * @deprecated since 1.6.0
     */
    protected $_configPath = 'factoryx_instagram/module_options/states';


    /**
     * @param $tag
     * @return null
     * * @deprecated since 1.6.0
     */
    public function getState($tag)
    {
        $states = unserialize(Mage::getStoreConfig($this->_configPath));
        if (isset($states[$tag])) {
            return $states[$tag];
        }
        return null;
    }

    /**
     * @param $tag
     * @param $state
     * @deprecated since 1.6.0
     */
    public function setState($tag, $state)
    {
        $states = unserialize(Mage::getStoreConfig($this->_configPath));
        $states[$tag] = $state;
        Mage::getModel('core/config')->saveConfig($this->_configPath, serialize($states));
    }

    /**
     * @param $url
     * @param $tag
     * @param $listId
     * @return bool
     */
    public function runUpdate($url, $tag, $listId)
    {
        Mage::helper('instagram')->log(sprintf("%s->url:%s|%s", __METHOD__, $url, $tag));
        $nextUrl = Mage::getSingleton('core/session')->getNextUrl();
        if (is_array($nextUrl) && array_key_exists($listId, $nextUrl) && array_key_exists($tag,$nextUrl[$listId]))
        {
            $url = $nextUrl[$listId][$tag] ? $nextUrl[$listId][$tag] : $url;
        }
        $result = $this->getImages($url, $tag, $listId);

        Mage::helper('instagram')->log(sprintf("%s->result: %s", __METHOD__, print_r($result, true)));
        if (isset($result['error'])) {
            return false;
        }

        if (array_key_exists('nextUrl',$result))
        {
            $nextUrl = Mage::getSingleton('core/session')->getNextUrl();
            $nextUrl[$listId][$tag] = $result['nextUrl'];
            Mage::getSingleton('core/session')->setNextUrl($nextUrl);
        }
        else
        {
            $nextUrl = Mage::getSingleton('core/session')->getNextUrl();
            unset($nextUrl[$listId][$tag]);
            if (empty($nextUrl[$listId]))
            {
                unset($nextUrl[$listId]);
            }
            if (empty($nextUrl))
            {
                Mage::getSingleton('core/session')->unsNextUrl();
            }
            else
            {
                Mage::getSingleton('core/session')->setNextUrl($nextUrl[$listId]);
            }
        }

        return true;
    }


    /**
     * @param $tags
     * @param $listId
     * @return bool
     */
    public function update($tags, $listId)
    {
        $responseStatus = true;
        foreach ($this->filterTags($tags) as $tag) {
            $endpointUrl = $this->getEndpointUrl($tag, 'tags');
            Mage::helper('instagram')->log(sprintf("%s->endpointUrl: %s", __METHOD__, $endpointUrl));
            $responseStatus = $responseStatus && $this->runUpdate($endpointUrl, $tag, $listId);
        }
        return $responseStatus;
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function getImagesByTag($tag){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getEndpointUrl($tag));
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
        return $data;
    }

    /**
     * @param $endpointUrl
     * @param $tag
     * @param $listId
     * @return array
     */
    protected function getImages($endpointUrl, $tag, $listId)
    {
        Mage::helper('instagram')->log(sprintf("%s->url:%s|%s", __METHOD__, $endpointUrl, $tag));
        $helper = Mage::helper('instagram');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpointUrl);
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

        Mage::helper('instagram')->log(sprintf("%s->output:%s", __METHOD__, $output));

        unset($output);

        $out = array();

        // any returned errors?
        if (!isset($data->meta, $data->meta->code) || $data->meta->code !== 200) {
            Mage::helper('instagram')->log(sprintf("%s->error:%s", __METHOD__, $data->meta->error_message));
            $out['error'] = $data->meta->error_message;
            unset($data);
            //$out['error'] = $this->__("Instagram connect error");
            return $out;
        }

        if (!isset($data->data)) {
            $out['error'] = $this->__("Instagram data not founded");
            return $out;
        }

        if (isset($data->pagination->next_url)) {
            $out['nextUrl'] = $data->pagination->next_url;
        }

        if (isset($data->pagination->next_max_tag_id)) {
            $out['nextMaxId'] = $data->pagination->next_max_tag_id;
        }

        $imageCount = 0;
        foreach ($data->data as $item)
        {
            $captionText = "";
            if($item->caption){
                $captionText = $item->caption->text;
            }

            $username    = $item->user->username;

            $standardResolutionUrl = $item->images->standard_resolution->url;
            $lowResolutionUrl      = $item->images->low_resolution->url;
            $thumbnailUrl          = $item->images->thumbnail->url;
            $systemId              = $item->id;

            $image = Mage::getModel('instagram/instagramimage');

            $image->setStandardResolutionUrl($standardResolutionUrl)
                ->setLowResolutionUrl($lowResolutionUrl)
                ->setThumbnailUrl($thumbnailUrl)
                ->setImageId($systemId)
                ->setUsername($username)
                ->setCaptionText($helper->cleanHashtag($captionText))
                ->setTag($tag)
                ->setListId($listId)
                ->setLikes(Mage::helper('instagram/likes')->getLikes($systemId))
                ->save();

            $imageCount++;
        }
        $out['count'] = $imageCount;

        return $out;
    }

    /**
     * @param $value
     * @return string
     */
    public function getEndpointUrl($value)
    {
        // previous end point with no token ???
        //$endpointUrl = 'https://api.instagram.com/v1/tags/' . ltrim($value, '#') . '/media/recent?client_id=' . $this->getClientId();
        $endpointUrl = 'https://api.instagram.com/v1/tags/' . ltrim($value, '#') . '/media/recent';        
        /** @var $helper FactoryX_Instagram_Helper_Data */
        $helper = Mage::helper('instagram');
        $accessToken = Mage::getModel('instagram/instagramauth')->getAccessToken();
        $endpointUrl = $helper->buildUrl($endpointUrl, array(
            'access_token'  => $accessToken,
            'client_id'     => $this->getClientId()
        ));
        return $endpointUrl;
    }

    /**
     * @return mixed
     */
    protected function getClientId()
    {
        return Mage::getStoreConfig('factoryx_instagram/module_options/client_id');
    }

    /**
     * @param $rawTags
     * @return array
     */
    public function filterTags($rawTags)
    {
        $tags = explode(',', $rawTags);

        $out = array();
        foreach ($tags as $tag) {
            $tag = ltrim(trim($tag), '#');
            if (!empty($tag)) {
                $out[] = '#' . $tag;
            }
        }
        return $out;
    }


}