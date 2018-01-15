<?php

namespace Broarm\Facebook;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use SilverStripe\Control\Controller;
use SilverStripe\Security\Security;

/**
 * Class CallbackController
 *
 * @package Broarm\Silverstripe\Facebook
 */
class CallbackController extends Controller
{
    private static $allowed_actions = ['authenticate'];

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
            $longLivedAccessToken = null;
            try {
                $longLivedAccessToken = $oAuthClient->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $exception) {
                user_error('Received no token from Facebook');
            }

            $currentUser = Security::getCurrentUser();
            if ($longLivedAccessToken) {
                $currentUser->setField('FB_ShortLivedAccessToken', (string)$accessToken);
                $currentUser->setField('FB_LongLivedAccessToken', (string)$longLivedAccessToken->getValue());
                $currentUser->setField('FB_LongLivedAccessTokenValidUntil', $longLivedAccessToken->getExpiresAt()->getTimestamp());

                try {
                    $currentUser->write();
                } catch (\Exception $exception) {
                    user_error($exception);
                }
            }

            $this->redirect("/admin/security/EditForm/field/Members/item/{$currentUser->ID}/edit");
        } else {
            user_error('No Access token is set');
            exit();
        }
    }
}