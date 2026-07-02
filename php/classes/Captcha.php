<?php

namespace TSJIPPY\CAPTCHA;

use TSJIPPY;

if (! defined('ABSPATH')) {
    exit;
}

abstract class Captcha
{
    public array $settings;
    public string $key;
    public string $secret;
    public bool $login;
    public bool $register;
    public bool $password;
    public bool $comment;

    /**
     * Add the captcha to a form element
     * 
     * @param bool $print Whether to print the HTML or return it
     * @param string $extraData Extra data to add to the captcha div
     * @param string $class Extra class to add to the captcha div
     * 
     * @return string The HTML for the captcha 
     */
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

    /**
     * Get the HTML for the captcha
     *
     * @param bool $print Whether to print the HTML or return it
     * @param string $extraData Extra data to add to the captcha div
     * @param string $class Extra class to add to the captcha div
     *
     * @return string The HTML for the captcha
     */
    abstract function getHtml($print, $extraData = '', $class = '');

    /**
     * Add the captcha to a form element
     *
     * @param array $args The arguments for the form element
     *
     * @return array The altered arguments for the form element
     */
    public function addFormElement($args)
    {
        $html                 = $this->addHtml(false);
        $args['submit_field'] = $html . $args['submit_field'];

        return $args;
    }

    /**
     * Generic function to retrieve token status for captchas
     * 
     * @param string $verifyUrl The URL to verify the token
     * @param array $data The data to send to the verification URL
     * 
     * @return object The response from the verification URL
     */
    public function verifyCaptcha($verifyUrl, $data)
    {
        $response   = wp_remote_post($verifyUrl, ['body' => $data]);

        return json_decode($response['http_response']->get_data());
    }

    /**
     * Verifies a turnstile token from $_REQUEST
     *
     * @return    bool|\WP_Error            false if no token found|WP_Error if invalid token, true is success
     */
    abstract function verify();
}
