<?php

namespace TSJIPPY\CAPTCHA;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Add the captcha HTML to the login form
 */
add_action('login_form', __NAMESPACE__ . '\loginForm');
function loginForm()
{
    $captcha    = determineCaptchaType();

    if ($captcha && $captcha->login) {
        $captcha->addHtml();
    }
}

/**
 * Verify the captcha result
 */
add_filter('authenticate', __NAMESPACE__ . '\loginFormFilter', 99);
add_filter('tsjippy-login-after-user-check', __NAMESPACE__ . '\loginFormFilter', 99);
/**
 * Verify the captcha result
 * 
 * @param   \WP_User|\WP_Error    $user       The user object or an error object
 * @return  \WP_User|\WP_Error                The user object or an error object
 */
function loginFormFilter($user)
{
    if (is_wp_error($user)) {
        return $user;
    }

    return captchaVerification($user, 'login');
}

/**
 * Add the captcha HTML to the register form
 */
add_action('register_form', __NAMESPACE__ . '\registerForm');
/**
 * Add the captcha HTML to the register form
 */
function registerForm()
{
    $captcha    = determineCaptchaType();

    if ($captcha && $captcha->register) {
        $captcha->addHtml();
    }
}

/**
 * Verify the captcha result
 */
add_filter('registration_errors', __NAMESPACE__ . '\registrationFormfilter', 99);
/**
 * Verify the captcha result
 * 
 * @param   \WP_Error   $user       The user object or an error object
 * @return  \WP_Error               The user object or an error object
 */
function registrationFormfilter($user)
{
    return captchaVerification($user, 'register');
}

/**
 * Add the captcha HTML to the password reset form
 */
add_action('resetpass_form', __NAMESPACE__ . '\resetForm');
add_action('lostpassword_form', __NAMESPACE__ . '\resetForm');
/**
 * Add the captcha HTML to the password reset form
 */
function resetForm()
{
    $captcha    = determineCaptchaType();

    if ($captcha && $captcha->password) {
        $captcha->addHtml();
    }
}

/**
 * Verify the captcha result
 */
add_filter('lostpassword_errors', __NAMESPACE__ . '\passwordResetFormfilter', 99);
/**
 * Verify the captcha result
 * 
 * @param   \WP_Error   $user       The user object or an error object
 * @return  \WP_Error               The user object or an error object
 */
function passwordResetFormfilter($user)
{
    return captchaVerification($user, 'reset');
}

/**
 * Add the captcha HTML to the comment form
 */
add_action('comment_form', __NAMESPACE__ . '\commentForm');
/**
 * Add the captcha HTML to the comment form
 */
function commentForm()
{
    $captcha    = determineCaptchaType();

    if ($captcha && $captcha->comment) {
        $captcha->addHtml();
    }
}

/**
 * Verify the captcha result
 */
add_filter('pre_comment_approved', __NAMESPACE__ . '\commentFormfilter', 99);
/**
 * Verify the captcha result
 * 
 * @param   mixed       $approved   The comment approval status
 * @return  mixed                   The comment approval status
 */
function commentFormfilter($approved)
{
    return captchaVerification($approved, 'comment');
}


add_filter('tsjippy-login-menu-item', function ($html) {
    return str_replace("class='", "data-form='#login-modal' class='cf-turnstile ", $html);
}, 10, 2);

// reset the has run var
add_action('tsjippy-content-filter-reset-page', function () {
    global $tsjippyCaptchaHasRun;

    $tsjippyCaptchaHasRun = false;
});


/**
 * Checks which captcha class to use and returns an instance of it
 *
 * @return  bool|object     false if no captcha found, instance of the correct captcha otherwise
 */
function determineCaptchaType()
{
    $turnstileSettings   = SETTINGS['turnstile'] ?? [];
    $recaptchaSettings   = SETTINGS['recaptcha'] ?? [];

    if (!empty($turnstileSettings)) {
        return new Turnstile();
    } else if (!empty($recaptchaSettings)) {
        return new Recaptcha();
    } else {
        return false;
    }
}

/**
 * Checks if a captcha verification needs to be done, and performs it if needed
 *
 * @param   mixed           $var        The param received by the filter
 * @param   string          $formType   One of login, register, comment, or password
 * @return  object|\WP_Error             The received user object or an WP_error object if verification failed
 */
function captchaVerification($var, $formType)
{
    $captcha    = determineCaptchaType();

    if (empty($captcha->$formType)) {
        return $var;
    }

    $verficationResult  = $captcha->verify();

    if (is_wp_error($verficationResult)) {
        return $verficationResult;
    }

    return $var;
}

/**
 * Validates a Form Captcha Form Submit
 */
add_filter('tsjippy-forms-before-inserting-formdata', __NAMESPACE__ . '\verifyFormCaptcha', 10, 2);
/**
 * Validates a Form Captcha Form Submit
 *
 * @param   array   $request    The form request data
 * @param   object  $object     The form object
 * @return  array|object        The form request data or an error object if verification failed
 */
function verifyFormCaptcha($request, $object)
{
    if ($object->getElementByType('turnstile')) {
        $captcha    = new Turnstile();
    } elseif ($object->getElementByType('recaptcha')) {
        $captcha    = new Recaptcha();
    } else {
        return $request;
    }

    return $captcha->verify();
}
