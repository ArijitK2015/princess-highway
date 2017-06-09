<?php

class FactoryX_Instagram_Model_System_Config_Source_Scopes {

    private static $scopes = array(
        'basic',            // to read a user’s profile info and media
        'public_content',   // to read any public profile info and media on a user’s behalf
        'follower_list',    // to read the list of followers and followed-by users
        'comments',         // to post and delete comments on a user’s behalf
        'relationships',    // to follow and unfollow accounts on a user’s behalf
        'likes'             // to like and unlike media on a user’s behalf
    );

    public function toOptionArray()
    {
        $options = array();
        foreach(self::$scopes as $scope) {
            $options[] = array(
                'value' => $scope,
                'label' => $scope
            );
        }
        return $options;
    }
}
