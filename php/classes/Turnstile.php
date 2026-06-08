<?php

namespace TSJIPPY\CAPTCHA;

use TSJIPPY;

if (! defined('ABSPATH')) {
    exit;
}

class Turnstile extends Captcha
{
    public string $keyType;

    public function __construct()
    {
        $this->settings     = SETTINGS['turnstile'] ?? [];

        $this->key          = $this->settings['key'] ?? '';
        $this->keyType      = $this->settings['keytype'] ?? 'v2';
        $this->secret       = $this->settings['secretkey'] ?? '';

        $this->login        = $this->settings['login'] ?? false;
        $this->register     = $this->settings['newuser'] ?? false;
        $this->password     = $this->settings['password'] ?? false;
        $this->comment      = $this->settings['comment'] ?? false;

        // Use test keys on localhost
        if (wp_get_environment_type() === 'local') {
            $this->key          = '1x00000000000000000000AA'; // success
            #$this->key         = '2x00000000000000000000AB'; // fail
            $this->secret       = '1x0000000000000000000000000000000AA'; // success
            #$this->secretKey   = '2x0000000000000000000000000000000AA'; // fail
        }
    }

    public function getHtml($print = true, $extraData = '', $class = '')
    {
        global $tsjippyCaptchaHasRun;

        // Do not run twice
        if ($tsjippyCaptchaHasRun || empty($this->key)) {
            return $extraData;
        }

        $url    = "https://challenges.cloudflare.com/turnstile/v0/api.js"; // online url, disallowed by wp
        //$url    = TSJIPPY\pathToUrl(PLUGINPATH. 'js/turnstile.min.js'); // Does not work
        wp_enqueue_script('tsjippy_turnstile_api', "$url?render=explicit", [], 0.1, ['strategy' => 'defer', 'in_footer' => true]);
        wp_enqueue_script('tsjippy_turnstile', TSJIPPY\pathToUrl(PLUGINPATH . 'js/turnstile.min.js'), [], PLUGINVERSION, true);

        $tsjippyCaptchaHasRun    = true;

        if (!$print) {
            ob_start();
        }

        ?>

        <style>
            #login{
                width: 350px;
            }
        </style>
        
        <div class='cf-turnstile <?php echo esc_attr($class); ?>' <?php echo esc_attr($extraData); ?>></div>
        <?php

        if (!$print) {
            return ob_get_clean();
        }
    }

    /**
     * Verifies a turnstile token from $_REQUEST
     *
     * @return    bool|\WP_Error            false if no token found|WP_Error if invalid token, true is success
     */
    public function verify()
    {
        if (!isset($_REQUEST['cf-turnstile-response'])) {
            //return new \WP_Error('turnstile', "Invalid Turnstile Response!");
            return false;
        }

        $turnstileToken = TSJIPPY\sanitize($_REQUEST['cf-turnstile-response']);

        // Do not verify again if already verified
        if (\TSJIPPY\getFromTransient(substr($turnstileToken, 0, 170)) == $turnstileToken) {
            return true;
        }

        $verifyUrl     = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
        $data        = "secret={$this->secret}&response=$turnstileToken";

        $json        = $this->verifyCaptcha($verifyUrl, $data);

        if (empty($json->success)) {
            return new \WP_Error('turnstile', "Invalid Turnstile Response!");
        } else {
            \TSJIPPY\storeInTransient(substr($turnstileToken, 0, 170), $turnstileToken, MINUTE_IN_SECONDS);

            return true;
        }
    }
}
