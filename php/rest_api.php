<?php

namespace TSJIPPY\CAPTCHA;

if (! defined('ABSPATH')) {
    exit;
}

// Allow rest api urls for non-logged in users
add_filter('tsjippy_allowed_rest_api_urls', __NAMESPACE__ . '\addCaptchaUrls');
/**
 * Adds captcha URLs to the list of allowed REST API URLs
 *
 * @param array  $urls The list of allowed REST API URLs
 * @return array       The updated list of allowed REST API URLs
 */
function addCaptchaUrls($urls)
{
    $urls[] = TSJIPPY\RESTAPIPREFIX . '/captcha/get_turnstile_key';

    return $urls;
}

add_action('rest_api_init', __NAMESPACE__ . '\restApiInit');
function restApiInit()
{
    //Route for notification messages
    register_rest_route(
        TSJIPPY\RESTAPIPREFIX . '/captcha',
        '/get_turnstile_key',
        array(
            'methods'              => 'GET',
            'callback'             => function(){
                $turnstile  = new Turnstile();
                return $turnstile->key;
            },
            'permission_callback'  => '__return_true',
        )
    );
}