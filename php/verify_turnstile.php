<?php
namespace SIM\CAPTCHA;
use SIM;

if(isset($turnstileSettings['login']) && $turnstileSettings['login'] == 'on'){
    add_filter( 'authenticate', __NAMESPACE__.'\turnstileFilter', 99);
    add_filter( 'sim-after-user-check', __NAMESPACE__.'\turnstileFilter', 99);
}

if(isset($turnstileSettings['newuser']) && $turnstileSettings['newuser'] == 'on'){
    add_filter( 'registration_errors', __NAMESPACE__.'\turnstileFilter', 99 );
}

if(isset($turnstileSettings['password']) && $turnstileSettings['password'] == 'on'){
    add_filter( 'lostpassword_errors', __NAMESPACE__.'\turnstileFilter', 99 );
}

if(isset($turnstileSettings['comment']) && $turnstileSettings['comment'] == 'on'){
    add_filter( 'lostpassword_errors', __NAMESPACE__.'\turnstileFilter', 99 );
}

function turnstileFilter($user){
    $verficationResult  = verifyTurnstile();

    if(is_wp_error($verficationResult)){
        return $verficationResult;
    }

    return $user;
}

/**
 * Verifies a turnstile token from $_REQUEST
 *
 * @return	bool			false if no token found
 */
function verifyTurnstile(){
    if(!isset($_REQUEST['cf-turnstile-response'])){
        return new \WP_Error('turnstile', "Invalid Turnstile Response!");
    }

    $turnstileToken = sanitize_text_field($_REQUEST['cf-turnstile-response']);

    if(!isset($_SESSION)){
		session_start();
    }

    // Do not verify again if already verified
    if(get_transient( substr($turnstileToken, 0, 170) ) == $turnstileToken){
        return true;
    }

    global $turnstileSettings;

    $secret		= $turnstileSettings['secretkey'];
    $verifyUrl 	= "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    $data		= "secret=$secret&response=$turnstileToken";

    $json	    = verifyCaptcha($verifyUrl, $data);

    if(empty($json->success)){
        return new \WP_Error('turnstile', "Invalid Turnstile Response!");
    }else{
        set_transient( substr($turnstileToken, 0, 170), $turnstileToken, MINUTE_IN_SECONDS );

        return true;
    }
}