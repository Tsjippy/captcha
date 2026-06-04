<?php
namespace TSJIPPY\CAPTCHA;

if ( ! defined('ABSPATH')) {
    exit;
}

class ReCaptcha extends Captcha{
    public $secretKey;
    public $keyType;

    public function __construct() {
        $this->settings   = SETTINGS['turnstile'] ?? [];

        $this->key        = $this->settings['key'];
        $this->keyType    = $this->settings['keytype'] ?? 'v2';
        $this->secretKey  = $this->settings['secretkey'];
    }

    public function getHtml($print=true, $extraData='', $class='') {
        if (!$this->key) {
            return;
        }

        if (!$print) {
            ob_start();
        }

        if ($this->keyType == 'v2') {
            wp_enqueue_script('tsjippy_recaptcha_v2', "https://www.google.com/recaptcha/api.js", [], PLUGINVERSION, ['strategy' => 'defer', 'in_footer' => true]);

            ?>
            <div class='g-recaptcha $class' data-sitekey='<?php echo esc_attr($this->key);?>' <?php echo esc_attr($extraData);?> required>
            </div>
            <?php
        }else{
            wp_enqueue_script('tsjippy_recaptcha_v3', "https://www.google.com/recaptcha/api.js?render=$this->key&onload=onloadCallback", [], PLUGINVERSION, ['strategy' => 'defer', 'in_footer' => true]);
            ob_start();
            ?>
            <input type='hidden' class='no-reset' name='g-recaptcha-response' id='g-recaptcha-response'>
            <script>
                document.querySelectorAll(' .submit-wrapper .form-submit').foreach (el=>el.disabled=true);
                function onloadCallback() {
                    grecaptcha.ready(function () {
                        setInterval(function () {
                            grecaptcha.execute('<?php echo esc_attr($this->key);?>', {action: 'validate_captcha'}).then(function (token) {
                                document.querySelectorAll(' .submit-wrapper .form-submit[disabled]').foreach (el=>el.disabled=false);
                                console.log('refreshed token:', token);
                                document.getElementById('g-recaptcha-response').value = token;
                            });
                        }, 60000);
                    });
                }
            </script>
            <?php
        }

        if (!$print) {
            return ob_get_clean();
        }
    }

    /**
     * Verifies a recaptcha token from $_REQUEST
     */
    public function verify() {
        if (empty($_REQUEST['g-recaptcha-response'])) {
            return false;
        }

        $verifyUrl     = 'https://www.google.com/recaptcha/api/siteverify';

        $queryData = [
            'secret'     => $this->secret,
            'response'     => sanitize_text_field(wp_unslash($_REQUEST['g-recaptcha-response'])),
            'remoteip'     => sanitize_text_field(wp_unslash((isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'])))
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