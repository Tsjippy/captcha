<?php
namespace TSJIPPY\CAPTCHA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Captcha{
    public $settings;
    public $key;
    public $secret;
    public $login;
    public $register;
    public $password;
    public $comment;


    public function __construct(){
    }

    public function addHtml($print=true, $extraData='', $class=''){
        // If the action we are hooking in was called more than once, return.
        if ( 
            did_action( 'login_form' ) > 1 ||
            did_action( 'register_form' ) > 1 ||
            did_action( 'resetpass_form' ) > 1 
        ) {
            return;
        }

        return $this->getHtml($print, $extraData, $class);
    }

    abstract function getHtml($print, $extraData='', $class='');

    public function addFormElement($args){
        $html                 = $this->addHtml(false);
        $args['submit_field'] = $html.$args['submit_field'];

        return $args;
    }

    /**
    * Generic function to retrieve token status for captchas
    */
    public function verifyCaptcha($verifyUrl, $data){
        if (function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec')){
            // Use cURL to get data 10x faster than using file_get_contents or other methods
            $ch = curl_init($verifyUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-type: application/x-www-form-urlencoded'));
            $response = curl_exec($ch);
            curl_close($ch);
        }else{
            // If server not have active cURL module, use file_get_contents
            $opts = array('http' =>
                array(
                    'method' 	=> 'POST',
                    'header'	=> 'Content-type: application/x-www-form-urlencoded',
                    'content' 	=> $data
                )
            );
            $context 	= stream_context_create($opts);
            $response 	= file_get_contents($verifyUrl, false, $context);
        }

        return json_decode($response);
    }

    /**
    * Verifies a turnstile token from $_REQUEST
    *
    * @return	bool|WP_Error			false if no token found|WP_Error if invalid token, true is success
    */
    abstract function verify();
}