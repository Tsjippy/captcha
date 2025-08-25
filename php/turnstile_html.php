<?php
namespace SIM\CAPTCHA;
use SIM;

function getTurnstileHtml($extraData='', $class=''){
    global $turnstileSettings;

    $html           = '';

    if($turnstileSettings && !empty($turnstileSettings["key"])){
        wp_enqueue_script('sim_turnstile');

        printJsTurnstile();

        $html	.= "<div class='cf-turnstile $class' $extraData></div>";
    }

    return $html;
}

function printJsTurnstile(){
    global $turnstileSettings;
    global $hasRun;

    // Do not run twice
    if($hasRun){
        return;
    }   

    $hasRun    = true;
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.cf-turnstile.now').forEach(el => loadTurnstile(el));
        });

        document.addEventListener("click", async function(event){
            let target  = event.target;
            let form    = target.closest('form');
            if(form != null || target.classList.contains('cf-turnstile')){
                if(form == null){
                    form = document.querySelector(target.dataset.form);
                }

                let turnstile = form.querySelector('div.cf-turnstile');

                // Only load if not already loaded
                if(turnstile != null && turnstile.innerHTML == ''){
                    if(turnstile.closest('.hidden') != null){
                        turnstile.closest('.hidden').classList.remove('hidden');
                    }

                    // Disable form submit 
                    form.querySelectorAll('button').forEach(button => button.disabled = true);

                    form.querySelectorAll('.button').forEach(button => button.classList.add('hidden'));

                    // Load the turnstile
                    loadTurnstile(turnstile);
                }
            }
        });

        function loadTurnstile(target) {
            turnstile.render(target, {
                sitekey: '<?php echo $turnstileSettings["key"];?>',
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
}

// reset the has run var
add_action('sim-content-filter-reset-page', function(){
    global $hasRun;

    $hasRun = false;
});