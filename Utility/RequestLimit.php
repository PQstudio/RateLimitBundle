<?php
namespace PQstudio\RateLimitBundle\Utility;


class RequestLimit
{
    protected $limit;

    protected $remaining;

    protected $reset;

    protected $isLimit;

    public function __construct()
    {
        $this->isLimit = false;
    }

    /**
     * Get limit.
     *
     * @return limit.
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set limit.
     *
     * @param limit the value to set.
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Get remaining.
     *
     * @return remaining.
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * Set remaining.
     *
     * @param remaining the value to set.
     */
    public function setRemaining($remaining)
    {
        $this->remaining = $remaining;
    }

    /**
     * Get reset.
     *
     * @return reset.
     */
    public function getReset()
    {
        return $this->reset;
    }

    /**
     * Set reset.
     *
     * @param reset the value to set.
     */
    public function setReset($reset)
    {
        $this->reset = $reset;
    }

    /**
     * Get isLimit.
     *
     * @return isLimit.
     */
    public function getIsLimit()
    {
        return $this->isLimit;
    }

    /**
     * Set isLimit.
     *
     * @param isLimit the value to set.
     */
    public function setIsLimit($isLimit)
    {
        $this->isLimit = $isLimit;
    }
}
