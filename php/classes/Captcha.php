<?php

namespace TSJIPPY\CAPTCHA;

use TSJIPPY;

if (! defined('ABSPATH')) {
    exit;
}

abstract class Captcha
{
    public $settings;
    public $key;
    public $secret;
    public $login;
    public $register;
    public $password;
    public $comment;


    public function __construct() {}

    public function addHtml($print = true, $extraData = '', $class = '')
    {
        // If the action we are hooking in was called more than once, return.
        if (
            did_action('login_form') > 1 ||
            did_action('register_form') > 1 ||
            did_action('resetpass_form') > 1
        ) {
            return;
        }

        return $this->getHtml($print, $extraData, $class);
    }

    abstract function getHtml($print, $extraData = '', $class = '');

    public function addFormElement($args)
    {
        $html                 = $this->addHtml(false);
        $args['submit_field'] = $html . $args['submit_field'];

        return $args;
    }

    /**
     * Generic function to retrieve token status for captchas
     */
    public function verifyCaptcha($verifyUrl, $data)
    {
        $response   = wp_remote_post($verifyUrl, ['body' => $data]);

        return json_decode($response['http_response']->get_data());
    }

    /**
     * Verifies a turnstile token from $_REQUEST
     *
     * @return    bool|WP_Error            false if no token found|WP_Error if invalid token, true is success
     */
    abstract function verify();
}
