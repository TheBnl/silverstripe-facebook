<?php

namespace Broarm\Silverstripe\Facebook;

use Controller;
use Member;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

/**
 * Class CallbackController
 * @package Broarm\Silverstripe\Facebook
 */
class CallbackController extends Controller
{
    private static $allowed_actions = array('authenticate');

    public function init()
    {
        parent::init();
    }

    /**
     * Authenticate the user with the Facebook API
     */
    public function authenticate()
    {
        $facebook = new SilverstripeFacebook();
        $helper = $facebook->instance()->getRedirectLoginHelper();
        $oAuthClient = $facebook->instance()->getOAuth2Client();

        try {
            $accessToken = (string)$helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            user_error('Graph returned an error: ' . $e->getMessage());
            exit;
        } catch (FacebookSDKException $e) {
            user_error('Facebook SDK returned an error: ' . $e->getMessage());
            exit;
        }

        if (isset($accessToken)) {
            $longLivedAccessToken = $oAuthClient->getLongLivedAccessToken($accessToken);
            $currentUser = Member::currentUser();
            $currentUser->setField('FB_ShortLivedAccessToken', (string)$accessToken);
            $currentUser->setField('FB_LongLivedAccessToken', (string)$longLivedAccessToken->getValue());
            $currentUser->setField('FB_LongLivedAccessTokenValidUntil', $longLivedAccessToken->getExpiresAt()->getTimestamp());
            $currentUser->write();
            
            $memberID = Member::currentUserID();
            $this->redirect("/admin/security/EditForm/field/Members/item/{$memberID}/edit");
        } else {
            user_error('No Access token is set');
            exit();
        }
    }
}