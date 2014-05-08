<?php
namespace PQstudio\RateLimitBundle\Storage;

use Symfony\Component\Security\Core\SecurityContext;
use PQstudio\RateLimitBundle\Exception\RateLimitException;

class RedisStorage
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $security;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $remaining;

    /**
     * @var int
     */
    private $reset;

    private $requestLimit;

    public function __construct($redis, SecurityContext $security, $requestLimit)
    {
        $this->redis = $redis;
        $this->security= $security;
        $this->requestLimit = $requestLimit;
    }

    public function checkRequest($path, $ip, $limit, $inTime)
    {
        $key = $path.$ip;

        if($this->redis->exists($key)) {
            $this->limit = $this->redis->get($key);

            $this->reset = time() + $this->redis->ttl($key);
        } else {
            $this->redis->set($key, $this->limit = $limit);
            $this->redis->expire($key, $inTime);

            $this->reset = time() + $inTime;
        }

        if($this->limit > 0) {
            $remaining = $this->redis->decr($key);
            $this->remaining = $remaining;

            $this->requestLimit->setLimit($limit);
            $this->requestLimit->setRemaining($this->remaining);
            $this->requestLimit->setReset($this->reset);
            $this->requestLimit->setIsLimit(true);
        } else {
            $this->remaining = 0;
            throw new RateLimitException(
                $limit,
                $this->remaining,
                $this->reset
            );
        }
    }
}

