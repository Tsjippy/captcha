<?php

namespace TSJIPPY\CAPTCHA;

use TSJIPPY;
use TSJIPPY\ADMIN;

if (! defined('ABSPATH')) {
    exit;
}

class AdminMenu extends ADMIN\SubAdminMenu
{

    /**
     * AdminMenu constructor.
     *
     * @param array $settings The settings for the plugin
     * @param string $name The name of the plugin
     */
    public function __construct($settings, $name)
    {
        parent::__construct($settings, $name);
    }

    /**
     * Add the settings page to the admin menu
     *
     * @param string $parent The parent menu slug
     * @return bool True if the settings page was added, false otherwise
     */
    public function settings($parent)
    {
        wp_enqueue_script('tsjippy_captcha_admin', TSJIPPY\pathToUrl(PLUGINPATH . 'js/admin.min.js'), array(), PLUGINVERSION, true);

        ob_start();
        ?>
        <h4 style="margin-bottom: 0px;">Choose Captcha Engine</h4>
        <label>
            <input type="radio" id="recaptcha" name="captcha" value="recaptcha" <?php echo ($this->settings['captcha'] ?? '') == 'recaptcha' ? 'checked' : ''; ?>>
            reCaptcha by Google (<a href='https://www.google.com/recaptcha/admin/create' target='_blank'>See here</a>)
        </label>
        <br>
        <label>
            <input type="radio" id="turnstile" name="captcha" value="turnstile" <?php echo ($this->settings['captcha'] ?? '') == 'turnstile'  ? 'checked' : ''; ?>>
            Turnstile by Cloudflare (<a href='https://www.cloudflare.com/products/turnstile/' target='_blank'>See here</a>)
        </label>

        <br>
        <br>

        <div class="captcha-options-wrapper recaptcha <?php echo esc_attr(($this->settings['captcha'] ?? 'recaptcha') != 'recaptcha' ? 'hidden' : ''); ?>">
            <label>
                Your API key<br>
                <input type='text' name='recaptcha[key]' value='<?php echo esc_attr($this->settings['recaptcha']["key"] ?? ''); ?>' style='width:350px'>
            </label>
            <br>
            <br>
            <label>
                API key type<br>
                <label>
                    <input type='radio' name='recaptcha[keytype]' value='v2' <?php if (($this->settings['recaptcha']["keytype"] ?? '') == 'v2') echo 'checked'; ?>>
                    v2
                </label>
                <label>
                    <input type='radio' name='recaptcha[keytype]' value='v3' <?php if (($this->settings['recaptcha']["keytype"] ?? '') == 'v3') echo 'checked'; ?>>
                    v3 / Enterprise
                </label>
            </label>
            <br>
            <br>
            <label>
                Your secret key<br>
                <input type='text' name='recaptcha[secret]' value='<?php echo esc_attr($this->settings['recaptcha']['secret'] ?? ''); ?>' style='width:350px'>
            </label>
            <br>
            <br>
            <table style='border:none'>
                <tr>
                    <td>
                        Use on login form
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="recaptcha[login]" <?php if (isset($this->settings['recaptcha']['login'])) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        Use on password reset form
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="recaptcha[password]" <?php if (isset($this->settings['recaptcha']['password'])) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        Use on new user form
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="recaptcha[newuser]" <?php if (isset($this->settings['recaptcha']['newuser'])) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        Use on comment form
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="recaptcha[comment]" <?php if (isset($this->settings['recaptcha']['comment'])) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <div class="captcha-options-wrapper turnstile <?php echo ($this->settings['captcha'] ?? 'recaptcha') != 'turnstile' ? 'hidden' : ''; ?>">
            <label>
                Your API key<br>
                <input type='text' name='turnstile[key]' value='<?php echo esc_attr($this->settings['turnstile']['key'] ?? ''); ?>' style='width:350px'>
            </label>
            <br>
            <label>
                Your secret key<br>
                <input type='text' name='turnstile[secretkey]' value='<?php echo esc_attr($this->settings['turnstile']['secretkey'] ?? ''); ?>' style='width:350px'>
            </label>
            <br>
            <br>
            <table style='border:none'>
                <tr>
                    <td>
                        Use on login form
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="turnstile[login]" value=1 <?php if (isset($this->settings['turnstile']['login'])) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        Use on password reset form
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="turnstile[password]" value=1 <?php if (isset($this->settings['turnstile']['password'])) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        Use on new user form
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="turnstile[newuser]" value=1 <?php if (isset($this->settings['turnstile']['newuser'])) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        Use on comment form
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="turnstile[comment]" value=1 <?php if (isset($this->settings['turnstile']['comment'])) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <?php

        TSJIPPY\addRawHtml(ob_get_clean(), $parent, 'beforeEnd');

        return true;
    }

    /**
     * Add the emails page to the admin menu
     *
     * @param string $parent The parent menu slug
     * @return bool True if the emails page was added, false otherwise
     */
    public function emails($parent)
    {
        return false;
    }

    /**
     * Add the data page to the admin menu
     *
     * @param string $parent The parent menu slug
     * 
     * @return bool True if the data page was added, false otherwise
     */
    public function data($parent)
    {
        return false;
    }

    /**
     * Add the functions page to the admin menu
     *
     * @param string $parent The parent menu slug
     * @return bool True if the functions page was added, false otherwise
     */
    public function functions($parent)
    {
        return false;
    }
}
