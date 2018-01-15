<?php

namespace Broarm\Facebook;

use SilverStripe\Control\Director;
use SilverStripe\Forms\LiteralField;

/**
 * Class AuthButton
 *
 * @package Broarm\Silverstripe\Facebook
 */
class AuthButton extends LiteralField
{

    public function __construct($name)
    {
        $facebook = new SilverstripeFacebook();
        $helper = $facebook->instance()->getRedirectLoginHelper();
        $loginURL = $helper->getLoginUrl(Director::absoluteURL('facebook/authenticate'));

        // TODO: make more button like and translate the label
        $label = _t('Facebook.AUTHENTICATE_LABEL', 'Authenticate with Facebook');
        $button = "<a href='{$loginURL}' class='btn action btn-primary'>$label</a>";

        parent::__construct($name, $button);
    }

}