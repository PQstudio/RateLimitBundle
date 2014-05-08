<?php

namespace PQstudio\RateLimitBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PQstudio\RateLimitBundle\DependencyInjection\PQstudioRateLimitExtension;

class PQstudioRateLimitBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new PQstudioRateLimitExtension();
    }
}
