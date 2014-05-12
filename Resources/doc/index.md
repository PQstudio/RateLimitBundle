Setting up the bundle
=============================

### A) Install RateLimitBundle

Add to your composer.json:

``` json
"pqstudio/rate-limit-bundle": "dev-master"
```

### B) Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new PQstudio\RateLimitBundle\PQstudioRateLimitBundle(),
    );
}
```

Basic configuration
===================
Bundle needs Redis instance as well as [SncRedisBundle](https://github.com/snc/SncRedisBundle) and [RecaptchaBundle](https://github.com/dmishh/RecaptchaBundle) for reCaptcha solver after rate limit kicks in.

You can configure limits for your routes in config.yml:

``` yaml
pq_rate_limit:
    limits:
        - { path: ^/users, method: ['GET'], limit: 100, time: 3600, captcha: true }
```

Above configuration allows for 100 GET requests in 3600 second timespan. After rate limit kicks in, it is possible to remove limit by resolving reCaptcha ([RecaptchaBundle](https://github.com/dmishh/RecaptchaBundle) is used for that functionality).

Client must render reCaptcha by itself ([Displaying reCaptcha](https://developers.google.com/recaptcha/docs/display)) and after user submits reCaptcha client should make ajax request:
```
GET /requestLimit/remove
{
    "challenge": "challenge_from_recaptcha",
    "response": "user_response_for_recaptcha"
}
```


Requests are limited by IP address.
