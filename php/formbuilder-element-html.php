<?php

namespace TSJIPPY\CAPTCHA;

use TSJIPPY;

if (! defined('ABSPATH')) {
    exit;
}

add_filter('tsjippy-forms-element-html-short-circuit', __NAMESPACE__ . '\addCaptchaHtml', 99, 3);
/**
 * Render the booking selector element on the form
 *
 * @param object $override  default null, return a node to skip element html rendering
 * @param object $parent The parent form element
 * @param object $object The form object
 *
 * @return object The rendered element
 */
function addCaptchaHtml($override, $parent, $object)
{
    $element    = $object->element;
    switch ($element->type) {
        case 'recaptcha':
            $html   = '';
            if (isset($_REQUEST['formbuilder'])) {
                $recaptchaKey        = SETTINGS['recaptchakey'] ?? '';
                if (!$recaptchaKey) {
                    $html    .= "Please enter your recaptcha key in the plugin settings";
                } else {
                    $html   .= "<img src'" . TSJIPPY\pathToUrl(PLUGINPATH . 'pictures/recaptcha.png') . "'>";
                }
            }

            $captcha    = new Recaptcha();

            $html   .= $captcha->addHtml(false);

            $override   = TSJIPPY\addRawHtml($html, $parent);
            break;
        case 'turnstile':
            $turnstilekey   = SETTINGS['turnstilekey'] ?? [];

            $html    = '';

            if (!$turnstilekey) {
                if (isset($_REQUEST['formbuilder'])) {
                    $html    = "Please enter your turnstile key in the plugin settings";
                }
            } else {
                $extraData    = '';
                if (!isset($_REQUEST['formbuilder']) && $element->hidden) {
                    $extraData            = "data-appearance='interaction-only'";
                    $element->hidden    = false;
                }

                $captcha    = new Turnstile();

                $html   = $captcha->addHtml(false, $extraData);
            }

            $override   = TSJIPPY\addRawHtml($html, $parent);
            break;
        default:
            return $override;
    }

    return $override;
}
