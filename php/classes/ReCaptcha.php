<?php

namespace TSJIPPY\CAPTCHA;

if (! defined('ABSPATH')) {
    exit;
}

class ReCaptcha extends Captcha
{
    public string $secretKey;
    public string $keyType;

    /**
     * ReCaptcha constructor.
     */
    public function __construct()
    {
        $this->settings   = SETTINGS['turnstile'] ?? [];

        $this->key        = $this->settings['key'];
        $this->keyType    = $this->settings['keytype'] ?? 'v2';
        $this->secretKey  = $this->settings['secretkey'];
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
    public function getHtml($print = true, $extraData = '', $class = '')
    {
        if (!$this->key) {
            return;
        }

        if (!$print) {
            ob_start();
        }

        if ($this->keyType == 'v2') {
            wp_enqueue_script('tsjippy_recaptcha_v2', "https://www.google.com/recaptcha/api.js", [], PLUGINVERSION, ['strategy' => 'defer', 'in_footer' => true]);

            ?>
            <div class='g-recaptcha $class' data-sitekey='<?php echo esc_attr($this->key); ?>' <?php echo esc_attr($extraData); ?> required>
            </div>
            <?php
        } else {
            wp_enqueue_script('tsjippy_recaptcha_v3', "https://www.google.com/recaptcha/api.js?render=$this->key&onload=onloadCallback", [], PLUGINVERSION, ['strategy' => 'defer', 'in_footer' => true]);

            wp_enqueue_script('tsjippy_recaptcha', TSJIPPY\pathToUrl(PLUGINPATH . 'js/recaptcha.min.js'), [], PLUGINVERSION, true);

            ?>
            <input type='hidden' class='no-reset' name='g-recaptcha-response' id='g-recaptcha-response'>
            <?php
        }

        if (!$print) {
            return ob_get_clean();
        }
    }

    /**
     * Verifies a recaptcha token from $_REQUEST
     */
    public function verify()
    {
        if (empty($_REQUEST['g-recaptcha-response'])) {
            return false;
        }

        $verifyUrl     = 'https://www.google.com/recaptcha/api/siteverify';

        $queryData = [
            'secret'     => $this->secret,
            'response'     => TSJIPPY\sanitize($_REQUEST['g-recaptcha-response']),
            'remoteip'     => TSJIPPY\sanitize((isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : ($_SERVER['REMOTE_ADDR'] ?? '')))
        ];

        // Collect and build POST data
        $data = http_build_query($queryData, '', '&');

        $json    = $this->verifyCaptcha($verifyUrl, $data);

        if (empty($json->success) || $json->score < 0.5) {
            return new \WP_Error('forms', "Invalid Google Response!");
        }

        return true;
    }
}
