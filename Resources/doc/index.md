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
Bundle needs Redis instance as well as [SncRedisBundle](https://github.com/snc/SncRedisBundle).

You can configure limits for your routes in config.yml:

``` yaml
pq_rate_limit:
    limits:
        - { path: ^/users, method: ['GET'], limit: 100, time: 3600 }
```

Above configuration allows for 100 GET requests in 3600 second timespan.

Requests are limited by IP address.
