<?php
namespace SIM\CAPTCHA;
use SIM;

function getTurnstileHtml($extraData=''){
    global $turnstileSettings;

    $html           = '';

    if($turnstileSettings && !empty($turnstileSettings["key"])){
        wp_enqueue_script('sim_turnstile');

        $html	.= "<div class='cf-turnstile' data-sitekey='{$turnstileSettings["key"]}' $extraData></div>";
    }

    return $html;
}