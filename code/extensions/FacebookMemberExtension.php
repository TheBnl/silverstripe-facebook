<?php
/**
 * FacebookMemberExtension.php
 *
 * @author Bram de Leeuw
 * Date: 25/11/16
 */
 
 
/**
 * FacebookMemberExtension
 */
class FacebookMemberExtension extends DataExtension {

    private static $db = array(
        'FB_ShortLivedAccessToken' => 'Varchar(255)',
        'FB_LongLivedAccessToken' => 'Varchar(255)',
        'FB_LongLivedAccessTokenValidUntil' => 'SS_Datetime'
    );

    private static $has_one = array();
    private static $has_many = array();
    private static $many_many = array();
    private static $defaults = array();
    private static $belongs_many_many = array();
    private static $searchable_fields = array();
    private static $summary_fields = array();
    private static $translate = array();

    public function updateCMSFields(FieldList $fields) {
        $fields->add(FacebookAuthButton::create('FBAuthButton'));
        return $fields;
    }


    /**
     * Get a facebook access token
     *
     * @return null
     */
    public function getFBAccessToken() {
        if ($accessToken = $this->owner->getField('FB_LongLivedAccessToken')) {
            return $accessToken;
        } else if ($accessToken = $this->owner->getField('FB_ShortLivedAccessToken')) {
            return $accessToken;
        } else {
            return null;
        }
    }
}


class FacebookAuthButton extends LiteralField
{

    public function __construct($name)
    {
        $facebook = new Facebook();
        $helper = $facebook->instance()->getRedirectLoginHelper();
        $loginURL = $helper->getLoginUrl(Director::absoluteURL('facebook/authenticate'));

        // TODO: make more button like and translate the label
        $label = 'Authenticate';
        $button = "<a href='{$loginURL}' class=''>$label</a>";

        parent::__construct($name, $button);
    }

}