<?php
/**
 * iKantam LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the iKantam EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://magento.factoryx.com/store/license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * HouseConfigurator Module to newer versions in the future.
 *
 * @category   Ikantam
 * @package    Ikantam_HouseConfigurator
 * @author     iKantam Team <support@factoryx.com>
 * @copyright  Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license    http://magento.factoryx.com/store/license-agreement  iKantam EULA
 */
class FactoryX_Instagram_Helper_Image_User extends FactoryX_Instagram_Helper_Image
{
    const INSTAGRAM_API_USER_MEDIA_URL = 'https://api.instagram.com/v1/users/%userId%/media/recent/';
    const INSTAGRAM_API_USER_ID_URL = 'https://api.instagram.com/v1/users/search?q=%userId%';

    /**
     * @param $users
     * @param $listId
     * @return bool
     */
    public function update($users, $listId)
    {
        $responseStatus = true;
        foreach ($this->filterUsers(false, $users) as $userId) {
            $user_id = $this->getUserId($userId);

            $nextUrl = Mage::getSingleton('core/session')->getNextUrl();

            if ($user_id) {
                $endpointUrl = $nextUrl[$listId][$user_id] ?  $nextUrl[$listId][$user_id] : $this->getEndpointUrl($user_id);
            } else {
                $endpointUrl = $nextUrl[$listId][$userId] ? $nextUrl[$listId][$userId] : $this->getEndpointUrl($userId);
            }

            $response = $this->getImages($endpointUrl, '@' . $userId, $listId);

            if(isset($response['error'])){
                $responseStatus = false;
            }
            else{
                if (array_key_exists('nextUrl',$response))
                {
                    $nextUrl = Mage::getSingleton('core/session')->getNextUrl();
                    if ($user_id) {
                        $nextUrl[$listId][$user_id] = $response['nextUrl'];
                    }
                    else
                    {
                        $nextUrl[$listId][$userId] = $response['nextUrl'];
                    }
                    Mage::getSingleton('core/session')->setNextUrl($nextUrl);
                }
                else
                {
                    $nextUrl = Mage::getSingleton('core/session')->getNextUrl();
                    if ($user_id) {
                        unset($nextUrl[$listId][$user_id]);
                    }
                    else
                    {
                        unset($nextUrl[$listId][$userId]);
                    }
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
            }
        }
        return $responseStatus;
    }

    /**
     * @param $userId
     * @return null
     */
    public function getUserId($userId)
    {
        $url = $this->getSearchEndpointUrl($userId);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
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

        if (isset($data->data)) {
            $data2 = $data->data;
            if (isset($data2[0])) {
                $data3 = $data2[0];
                if (isset($data3->id)) {
                    return $data3->id;
                }
            }
        }

        return null;
    }

    /**
     * Get array of users, defined in config panels
     * @param bool $withPrefix
     * @param $rawUsers
     * @return array
     */
    public function filterUsers($withPrefix = true, $rawUsers)
    {
        $users = explode(',', $rawUsers);

        $out = array();
        foreach ($users as $user) {
            $user = ltrim(trim($user), '@');
            if (!empty($user)) {
                if($withPrefix){
                    $out[] = '@' . $user;
                } else {
                    $out[] = $user;
                }
            }
        }
        return $out;
    }

    /**
     * @param $userId
     * @return mixed|string
     */
    public function getSearchEndpointUrl($userId)
    {
        $endpointUrl = str_replace('%userId%', $userId, self::INSTAGRAM_API_USER_ID_URL);
        /** @var $helper FactoryX_Instagram_Helper_Data */
        $helper = Mage::helper('instagram');
        $accessToken = Mage::getModel('instagram/instagramauth')->getAccessToken();
        $endpointUrl = $endpointUrl . '&access_token=' . $accessToken;

        return $endpointUrl;
    }

    /**
     * @param $userId
     * @return mixed|string
     */
    public function getEndpointUrl($userId)
    {
        $endpointUrl = str_replace('%userId%', $userId, self::INSTAGRAM_API_USER_MEDIA_URL);
        /** @var $helper FactoryX_Instagram_Helper_Data */
        $helper = Mage::helper('instagram');
        $accessToken = Mage::getModel('instagram/instagramauth')->getAccessToken();
        $endpointUrl = $helper->buildUrl($endpointUrl, array(
            'access_token'  => $accessToken,
        ));
        Mage::helper('instagram')->log(sprintf("%s->endpointUrl=%s", __METHOD__, $endpointUrl), Zend_Log::DEBUG );
        return $endpointUrl;
    }

    /**
     * @param $endpointUrl
     * @param $tag
     * @param $listId
     * @return array
     */
    protected function getImages($endpointUrl, $tag, $listId)
    {
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

        unset($output);

        $out = array();

        if (!isset($data->meta, $data->meta->code) || $data->meta->code !== 200) {
            $out['error'] = $this->__("Instagram connect error");
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

        foreach ($data->data as $item)
        {
            $captionText = '';
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
        }

        return $out;
    }

}