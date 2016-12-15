<?php
/**
 * FacebookCallbackController.php
 *
 * @author Bram de Leeuw
 * Date: 25/11/16
 */
 

class FacebookCallbackController extends Controller
{
    private static $allowed_actions = array('authenticate');

    public function init()
    {
        parent::init();
    }
    
    
    public function authenticate() {
        $facebook = new Facebook();
        $helper = $facebook->instance()->getRedirectLoginHelper();
        $oAuthClient = $facebook->instance()->getOAuth2Client();

        try {
            $accessToken = (string) $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            user_error('Graph returned an error: ' . $e->getMessage());
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            user_error('Facebook SDK returned an error: ' . $e->getMessage());
            exit;
        }

        if (isset($accessToken)) {
            $longLivedAccessToken = $oAuthClient->getLongLivedAccessToken($accessToken);
            $currentUser = Member::currentUser();
            $currentUser->setField('FB_ShortLivedAccessToken', (string) $accessToken);
            $currentUser->setField('FB_LongLivedAccessToken', (string) $longLivedAccessToken->getValue());
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