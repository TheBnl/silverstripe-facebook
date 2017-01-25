<?php

namespace Broarm\Silverstripe\Facebook;

use Director;
use LiteralField;

/**
 * Class AuthButton
 * @package Broarm\Silverstripe\Facebook
 */
class AuthButton extends LiteralField
{

    public function __construct($name)
    {
        $facebook = new FB();
        $helper = $facebook->instance()->getRedirectLoginHelper();
        $loginURL = $helper->getLoginUrl(Director::absoluteURL('facebook/authenticate'));

        // TODO: make more button like and translate the label
        $label = _t('Facebook.AUTHENTICATE_LABEL', 'Authenticate with Facebook');
        $button = "<a href='{$loginURL}' class='ss-ui-button ui-button'>$label</a>";

        parent::__construct($name, $button);
    }

}