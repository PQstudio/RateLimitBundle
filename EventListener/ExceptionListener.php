<?php
namespace PQstudio\RateLimitBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use PQstudio\RateLimitBundle\Exception\RateLimitException;

class ExceptionListener
{
    protected $jmsSerializer;

    public function __construct($jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        //You get the exception object from the received event
        $exception = $event->getException();

        $response = new JsonResponse();

        if($exception instanceof RateLimitException) {
            $meta = [
                'code' => $exception->getStatusCode(),
                'error' => $exception->getError(),
                'errorMessage' => $exception->getErrorMessage(),
            ];

            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $response->setContent($this->jmsSerializer->serialize($meta, 'json'));

            $event->setResponse($response);
        }
    }
}
