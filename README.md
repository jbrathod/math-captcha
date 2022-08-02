# A simple math captcha for Laravel5

A captcha that will be used for multiple pages/places in single website.

# Installation Guide #

```
composer require jbrathod/math-captcha
```

Find the `providers` key in `config/app.php` and register the Captcha Service Provider.

```php
    'providers' => [
        // ...
        'Jbrathod\MathCaptcha\MathCaptchaServiceProvider',
    ]
```
for Laravel 5.1+
```php
    'providers' => [
        // ...
        Jbrathod\MathCaptcha\MathCaptchaServiceProvider::class,
    ]
```
# Usage #

### Add captcha image to form (Note : "contact-us" is page name)

```html
<img src="{{ app('mathcaptcha')->image('contact-us') }}"/>
```

### Validate captcha

Add `'captcha' => 'required|mathcaptcha:contact-us'` to rules array.


```php
$this->validate($request, [
    'captcha' => 'required|mathcaptcha:contact-us',
]);

```

### Reset captcha after form submit without errors

```php
app('mathcaptcha')->reset('contact-us'); 
```