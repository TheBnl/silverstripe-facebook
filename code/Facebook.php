<?php
/**
 * Facebook.php
 *
 * @author Bram de Leeuw
 * Date: 25/11/16
 */


class Facebook extends Object {

    private static $app_id = '';

    private static $app_secret = '';

    private static $page_id = '';

    protected $facebook;

    /**
     * Facebook constructor.
     */
    public function __construct()
    {
        $this->facebook = new Facebook\Facebook([
            'app_id' => self::config()->get('app_id'),
            'app_secret' => self::config()->get('app_secret'),
            'default_graph_version' => 'v2.5',
        ]);
        
        parent::__construct();
    }


    /**
     * Return a facebook instance
     *
     * @return \Facebook\Facebook
     */
    public function instance() {
        return $this->facebook;
    }


    /**
     * Get a graph node
     *
     * @param $path
     * @return \Facebook\FacebookResponse|null
     */
    public function get($path, $fields = array()) {
        if (!empty($fields)) {
            $path .= '?fields=' . implode(',', $fields);
        }

        if ($token = Facebook::get_access_token()) {
            $fb = $this->facebook;
            $fb->setDefaultAccessToken($token);
            return $fb->get($path);
        } else {
            return null;
        }
    }


    /**
     * Get the page data
     *
     * @return \Facebook\FacebookResponse|null
     */
    public function getPage() {
        $pageID = self::config()->get('page_id');
        return $this->get("/$pageID");
    }


    /**
     * Get a event list
     *
     * @return \Facebook\FacebookResponse|null
     */
    public function getPageEvents() {
        $pageID = self::config()->get('page_id');
        return $this->get("/$pageID/events");
    }



    /**
     * Get a available access token,
     * if there is a current user with an access token return that one first
     *
     * @return string|null
     */
    private static function get_access_token() {
        $members = Member::get()->filter(array(
            'FB_LongLivedAccessToken:not' => '',
            'FB_LongLivedAccessTokenValidUntil:GreaterThan' => date('Y-m-d')
        ));

        if ($member = Member::currentUser()) {
            return $member->getFBAccessToken();
        } else if ($members->count() && $member = $members->first()) {
            return $member->getFBAccessToken();
        } else {
            // TODO Prompt the user to reauthenticate
            user_error('No access token available');
            return null;
        }
    }


    /**
     * Get a config var
     *
     * @param $var
     * @return array|scalar
     * /
    private static function get_config($var) {
        return Config::inst()->get('Facebook', $var);
    } //*/
}