<?php

namespace Broarm\Silverstripe\Facebook;

use Facebook\Facebook;
use Member;
use Object;

/**
 * Class SilverstripeFacebook
 *
 * @package Broarm\Silverstripe\Facebook
 */
class SilverstripeFacebook extends Object
{

    private static $app_id = '';

    private static $app_secret = '';

    private static $page_id = '';

    /**
     * @var Facebook
     */
    protected $facebook;

    /**
     * Facebook constructor.
     */
    public function __construct()
    {
        if (!isset($this->facebook)) {
            $this->facebook = new Facebook([
                'app_id' => self::config()->get('app_id'),
                'app_secret' => self::config()->get('app_secret'),
                'default_graph_version' => 'v2.5',
            ]);
        }

        // TODO check if token and if valid, else stop facebook operations and notify user silently
        if ($token = SilverstripeFacebook::get_access_token()) {
            $this->facebook->setDefaultAccessToken($token);
        }

        parent::__construct();
    }


    /**
     * Return a facebook instance
     *
     * @return \Facebook\Facebook
     */
    public function instance()
    {
        return $this->facebook;
    }


    /**
     * Get a graph node
     *
     * @param       $path
     * @param array $params
     *
     * @return \Facebook\FacebookResponse|null
     */
    public function get($path, array $params = [])
    {
        return $this->facebook->sendRequest('GET', $path, $params);
    }


    /**
     * Get the page data
     *
     * @param null  $node
     * @param array $params
     *
     * @return \Facebook\FacebookResponse|null
     */
    public function getPage($node = null, array $params = [])
    {
        $pageID = self::config()->get('page_id');
        return $this->get("/$pageID/$node", $params);
    }


    /**
     * Get a event list
     *
     * @return \Facebook\FacebookResponse|null
     */
    public function getPageEvents()
    {
        return $this->getPage('events');
    }


    /**
     * Get a available access token,
     * if there is a current user with an access token return that one first
     *
     * @return string|null
     */
    private static function get_access_token()
    {
        $members = Member::get()->filter(array(
            'FB_LongLivedAccessToken:not' => '',
            'FB_LongLivedAccessTokenValidUntil:GreaterThan' => date('Y-m-d')
        ));

        if ($members->count() && $member = $members->first()) {
            return $member->getFBAccessToken();
        } else {
            // TODO Prompt the user to re-authenticate
            return null;
        }
    }


    /**
     * Check the access tokens date
     *
     * @return \SS_Datetime|\DBField
     */
    public static function access_token_valid_until()
    {
        $members = Member::get()->filter(array(
            'FB_LongLivedAccessToken:not' => '',
            'FB_LongLivedAccessTokenValidUntil:GreaterThan' => date('Y-m-d')
        ));

        if ($members->count() && $member = $members->first()) {
            return $member->dbObject('FB_LongLivedAccessTokenValidUntil');
        } else {
            return null;
        }
    }


    /**
     * Get a config var
     *
     * @param $var
     *
     * @return array|scalar
     * /
     * private static function get_config($var)
     * {
     * return Config::inst()->get('Facebook', $var);
     * } //*/
}
