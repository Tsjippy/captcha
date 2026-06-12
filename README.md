Captcha for forms made with tsjippy-formbuilder as well as on the wordpress default forms

== Description ==
This plugin makes it possible to enable and use captcha on forms made with the formbuilder as well as on the wordpress default forms (login, register, reset password, comment)

The project uses:
* Turnstile by cloudflare, under the CC-BY_SA-4.0 [license](https://developers.cloudflare.com/fundamentals/reference/policies-compliances/licenses/) [terms of use](https://www.cloudflare.com/website-terms/) [privacy policy](https://www.cloudflare.com/turnstile-privacy-policy/)
* Google Recaptcha and their external resources [license](https://github.com/google/recaptcha/blob/main/LICENSE). [terms of use](https://cloud.google.com/terms) [privacy policy](https://policies.google.com/privacy)

Which one of the two providers is your choice. You do not have to use both.  
To be able to use them you need to set up an account with them. See [here](https://www.google.com/recaptcha/admin/create) foor Google ReCaptcha and [here](https://www.cloudflare.com/products/turnstile/) for Cloudflare Turnstile.

== External Services ==
This plugin relies on either Google's ReCaptcha or Cloudflare's Turnstile to generate a challenge for users to comlete to verify they are human ánd send that the result of that to their servers to verify.

== Issues ==
Please file any issues on the wp forum or directly on Github: 
* [captcha](https://github.com/Tsjippy/captcha/issues)
* [shared functionality](https://github.com/Tsjippy/shared-functionality/issues)