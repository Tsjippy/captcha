<?php
namespace SIM\CAPTCHA;
use SIM;

function getTurnstileHtml($extraData=''){
    global $turnstileSettings;

    $html           = '';

    if($turnstileSettings && !empty($turnstileSettings["key"])){
        wp_enqueue_script('sim_turnstile');

        printJsTurnstile();

        $html	.= "<div class='cf-turnstile' $extraData></div>";
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
        document.addEventListener("click", async function(event){
            let target  = event.target;
            let form    = target.closest('form');
            if(form != null){
                let turnstile = form.querySelector('.cf-turnstile');

                // Only load if not already loaded
                if(turnstile != null && turnstile.innerHTML == ''){
                    if(turnstile.closest('.hidden') != null){
                        console.log('Turnstile is hidden, not loading');
                        return;
                    }

                    // Disable form submit 
                    form.querySelectorAll('button').forEach(button => button.disabled = true);

                    form.querySelectorAll('.button').forEach(button => button.classList.add('hidden'));

                    // Load the turnstile
                    loadTurnstile(form.querySelector(`.cf-turnstile`));
                }
            }
        });

        function loadTurnstile(target) {
            turnstile.render(target, {
                sitekey: '<?php echo $turnstileSettings["key"];?>',
                callback: function(token) {
                    // Enable form submit again
                    document.querySelectorAll('button').forEach(button => button.disabled = true);

                    document.querySelectorAll('.button.hidden').forEach(button => button.classList.remove('hidden'));

                    console.log('Challenge completed:', token);
                }
            });
        }
    </script>
    <?php
}