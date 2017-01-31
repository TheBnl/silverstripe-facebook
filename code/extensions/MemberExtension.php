<?php

namespace Broarm\Silverstripe\Facebook;

use DataExtension;
use FieldList;
use LiteralField;

/**
 * Class FacebookMemberExtension
 * @package Broarm\Silverstripe\Facebook
 *
 * @property \Member|MemberExtension owner
 * @property string FB_ShortLivedAccessToken
 * @property string FB_LongLivedAccessToken
 * @property string FB_LongLivedAccessTokenValidUntil
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
        if ($this->isAuthenticatedWithFacebook()) {
            $validUntil = $this->owner->dbObject('FB_LongLivedAccessTokenValidUntil');
            $label = _t('Facebook.AUTHENTICATED', 'Has valid authentication until {date}', null, array(
                'date' => $validUntil->Format('d-m-Y')
            ));
            $button = "<p class='message good'>$label</p>";
            $fields->addFieldsToTab('Root.Facebook', array(
                LiteralField::create('FBAuthenticated', $button),
            ));
        }

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
    
    public function isAuthenticatedWithFacebook() {
        /** @var \SS_Datetime $validUntil */
        if ($validUntil = $this->owner->dbObject('FB_LongLivedAccessTokenValidUntil')) {
            return $validUntil->InFuture();
        }

        return false;
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


