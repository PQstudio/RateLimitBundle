<?php
namespace PQstudio\RateLimitBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RateLimitException extends HttpException
{
    protected $moreInfo;

    protected $error;

    protected $errorMessage;

    protected $limit;

    protected $remaining;

    protected $reset;

    public function __construct($limit, $remaining, $reset, $moreInfo = null)
    {
        $code = 429;
        $error = "too_many_requests";
        $errorMessage = "You have executed too many requests. Please try again later.";
        parent::__construct($code);

        $this->statusCode = $code;
        $this->error = $error;
        $this->errorMessage = $errorMessage;
        $this->moreInfo = $moreInfo;
        $this->limit = $limit;
        $this->remaining = $remaining;
        $this->reset = $reset;
    }

    public function getHeaders()
    {
        return array(
            'X-Rate-Limit-Limit'     => $this->limit,
            'X-Rate-Limit-Remaining' => $this->remaining,
            'X-Rate-Limit-Reset'     => $this->reset
        );
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getMoreInfo()
    {
        return $this->moreInfo;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getRemaining()
    {
        return $this->remaining;
    }

    public function getReset()
    {
        return $this->reset;
    }
}

