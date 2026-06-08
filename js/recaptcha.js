document.querySelectorAll('.submit-wrapper .form-submit').forEach(el => el.disabled = true);

function onloadCallback() {
    grecaptcha.ready(function() {
        setInterval(function() {
            grecaptcha.execute('<?php echo esc_attr($this->key); ?>', {
                action: 'validate_captcha'
            }).then(function(token) {
                document.querySelectorAll('.submit-wrapper .form-submit[disabled]').forEach(el => el.disabled = false);
                console.log('refreshed token:', token);
                document.getElementById('g-recaptcha-response').value = token;
            });
        }, 60000);
    });
}