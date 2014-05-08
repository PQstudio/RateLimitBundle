<?php
namespace PQstudio\RateLimitBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;

class RateLimitResponseListener
{
    private $requestLimit;

    protected $map = array();

    public function __construct($requestLimit)
    {
        $this->requestLimit = $requestLimit;
    }

    private function setHeaders(Response $response)
    {
        $limit = $this->requestLimit->getLimit();
        $remaining = $this->requestLimit->getRemaining();
        $reset = $this->requestLimit->getReset();

        $response->headers->add(array(
            'X-Rate-Limit-Limit'     => $limit,
            'X-Rate-Limit-Remaining' => $remaining,
            'X-Rate-Limit-Reset'     => $reset
        ));
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if(HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType())
            return;

        if($this->requestLimit->getIsLimit()) {
            $this->setHeaders($event->getResponse());
        }
    }
}
