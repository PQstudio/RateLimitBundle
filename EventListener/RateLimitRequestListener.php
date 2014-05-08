<?php
namespace PQstudio\RateLimitBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RateLimitRequestListener
{
    private $requestLimit;
    private $storage;

    protected $map = [];

    public function __construct($requestLimit, $storage)
    {
        $this->requestLimit = $requestLimit;
        $this->storage = $storage;
    }

    public function add(RequestMatcherInterface $requestMatcher, array $options = [])
    {
        $this->map[] = [$requestMatcher, $options];
    }

    private function setHeaders(Response $response)
    {
        $limit = $this->requestLimit->getLimit();
        $remaining = $this->requestLimit->getRemaining();
        $reset = $this->requestLimit->getReset();

        $response->headers->add([
            'X-Rate-Limit-Limit'     => $limit,
            'X-Rate-Limit-Remaining' => $remaining,
            'X-Rate-Limit-Reset'     => $reset
        ]);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if(HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType())
            return;

        $request = $event->getRequest();

        $options = $this->getOptions($request);

        if ($options) {
            $path = $request->getUri();
            $ip = $request->server->get('REMOTE_ADDR');
            $this->storage->checkRequest($path, $ip, $options['limit'], $options['time']);
        }
    }

    protected function getOptions(Request $request)
    {
        foreach ($this->map as $elements) {
            if (null === $elements[0] || $elements[0]->matches($request)) {
                return $elements[1];
            }
        }

        return [];
    }
}
