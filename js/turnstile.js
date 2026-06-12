document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.cf-turnstile.now').forEach(el => loadTurnstile(el));

    document.querySelectorAll(`#login`).forEach(el => { el.style.width = '350px';});
});

// Load turnstile as soon as we click on the form
document.addEventListener("click", async function(event) {
    let target = event.target;
    let form = target.closest('form');
    if (form != null || target.classList.contains('cf-turnstile')) {
        if (form == null) {
            form = document.querySelector(target.dataset.form);
        }

        let turnstileDiv = form.querySelector('div.cf-turnstile');

        // Only load if not already loaded
        if (turnstileDiv != null && turnstileDiv.innerHTML == '') {
            event.stopImmediatePropagation();

            if (turnstileDiv.closest('.hidden') != null) {
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

async function loadTurnstile(target) {
    let response = await FormSubmit.fetchRestApi(
      "captcha/get_turnstile_key"
    );

    turnstile.render(target, {
        sitekey: response,
        callback: function(token) {
            // Enable form submit again
            document.querySelectorAll('button').forEach(button => button.disabled = false);

            document.querySelectorAll('.button.hidden').forEach(button => button.classList.remove('hidden'));

            console.log('Challenge completed:', token);
        }
    });
}