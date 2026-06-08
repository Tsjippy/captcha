function captchaSelected(e) {
    document.querySelectorAll(`.captcha-options-wrapper`).forEach(el => el.classList.add("hidden"));
    document.querySelector(`.captcha-options-wrapper.${e.target.value}`).classList.remove("hidden");
}

document.getElementById("recaptcha").addEventListener('change', captchaSelected);

document.getElementById("turnstile").addEventListener('change', captchaSelected);