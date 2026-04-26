<?php
namespace TSJIPPY\CAPTCHA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Turnstile extends Captcha{
    public $keyType;

    public function __construct(){
        $this->settings     = SETTINGS['turnstile'] ?? [];

        $this->key          = $this->settings['key'];
        $this->keyType      = $this->settings['keytype'] ?? 'v2';
        $this->secret       = $this->settings['secretkey'];

        $this->login        = $this->settings['login'] ?? false;
        $this->register     = $this->settings['newuser'] ?? false;
        $this->password     = $this->settings['password'] ?? false;
        $this->comment      = $this->settings['comment'] ?? false;

        // Use test keys on localhost
        if(wp_get_environment_type() === 'local') {
            $this->key          = '1x00000000000000000000AA'; // success
            #$this->key         = '2x00000000000000000000AB'; // fail
            $this->secret       = '1x0000000000000000000000000000000AA'; // success
            #$this->secretKey   = '2x0000000000000000000000000000000AA'; // fail
        }
    }

    public function getHtml($print=true, $extraData='', $class=''){
        global $hasRun;

        // Do not run twice
        if($hasRun || empty($this->key )){
            return $extraData;
        }

        wp_enqueue_script('tsjippy_turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit', [], PLUGINVERSION, ['strategy' => 'defer', 'in_footer' => true]);

        $hasRun    = true;

        if(!$print){
            ob_start();
        }

        ?>
        <div class='cf-turnstile <?php echo $class;?>' <?php echo $extraData;?>></div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.cf-turnstile.now').forEach(el => loadTurnstile(el));
            });

            // Load turnstile as soon as we click on the form
            document.addEventListener("click", async function(event){
                let target  = event.target;
                let form    = target.closest('form');
                if(form != null || target.classList.contains('cf-turnstile')){
                    if(form == null){
                        form = document.querySelector(target.dataset.form);
                    }

                    let turnstileDiv = form.querySelector('div.cf-turnstile');

                    // Only load if not already loaded
                    if(turnstileDiv != null && turnstileDiv.innerHTML == ''){
                        event.stopImmediatePropagation();
                        
                        if(turnstileDiv.closest('.hidden') != null){
                            turnstileDiv.closest('.hidden').classList.remove('hidden');
                        }

                        // Disable form submit 
                        form.querySelectorAll('button').forEach(button => button.disabled = true);

                        form.querySelectorAll('.button').forEach(button => button.classList.add('hidden'));

                        // Load the turnstile
                        loadTurnstile(turnstileDiv);
                    }
                }
            });

            function loadTurnstile(target) {
                turnstile.render(target, {
                    sitekey: '<?php echo $this->key;?>',
                    callback: function(token) {
                        // Enable form submit again
                        document.querySelectorAll('button').forEach(button => button.disabled = false);

                        document.querySelectorAll('.button.hidden').forEach(button => button.classList.remove('hidden'));

                        console.log('Challenge completed:', token);
                    }
                });
            }
        </script>
        <?php

        if(!$print){
            return ob_get_clean();
        }
    }

    /**
    * Verifies a turnstile token from $_REQUEST
    *
    * @return	bool|WP_Error			false if no token found|WP_Error if invalid token, true is success
    */
    public function verify(){
        if(!isset($_REQUEST['cf-turnstile-response'])){
            //return new \WP_Error('turnstile', "Invalid Turnstile Response!");
            return false;
        }

        $turnstileToken = sanitize_text_field(wp_unslash($_REQUEST['cf-turnstile-response']));

        // Do not verify again if already verified
        if(\TSJIPPY\getFromTransient(substr($turnstileToken, 0, 170)) == $turnstileToken){
            return true;
        }

        $verifyUrl 	= "https://challenges.cloudflare.com/turnstile/v0/siteverify";
        $data		= "secret={$this->secret}&response=$turnstileToken";

        $json	    = $this->verifyCaptcha($verifyUrl, $data);

        if(empty($json->success)){
            return new \WP_Error('turnstile', "Invalid Turnstile Response!");
        }else{
            \TSJIPPY\storeInTransient( substr($turnstileToken, 0, 170), $turnstileToken, MINUTE_IN_SECONDS );

            return true;
        }
    }
}
