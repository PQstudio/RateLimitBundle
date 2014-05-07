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

