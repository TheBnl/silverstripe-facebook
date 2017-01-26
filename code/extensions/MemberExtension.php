<?php

namespace Broarm\Silverstripe\Facebook;

use DataExtension;
use FieldList;

/**
 * Class FacebookMemberExtension
 * @package Broarm\Silverstripe\Facebook
 *
 * @property \Member|MemberExtension owner
 * @property string FB_ShortLivedAccessToken
 * @property string FB_LongLivedAccessToken
 * @property \SS_Datetime FB_LongLivedAccessTokenValidUntil
 */
class MemberExtension extends DataExtension
{
    private static $db = array(
        'FB_ShortLivedAccessToken' => 'Varchar(255)',
        'FB_LongLivedAccessToken' => 'Varchar(255)',
        'FB_LongLivedAccessTokenValidUntil' => 'SS_Datetime'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.Facebook', array(
            AuthButton::create('FBAuthButton')
        ));

        $fields->removeByName(array(
            'FB_ShortLivedAccessToken',
            'FB_LongLivedAccessToken',
            'FB_LongLivedAccessTokenValidUntil'
        ));

        return $fields;
    }


    /**
     * Get a facebook access token
     * @return string|null
     */
    public function getFBAccessToken()
    {
        if ($accessToken = $this->owner->getField('FB_LongLivedAccessToken')) {
            return $accessToken;
        } elseif ($accessToken = $this->owner->getField('FB_ShortLivedAccessToken')) {
            return $accessToken;
        } else {
            return null;
        }
    }
}


