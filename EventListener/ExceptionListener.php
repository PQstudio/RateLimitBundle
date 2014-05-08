<?php
namespace PQstudio\RateLimitBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use PQstudio\RateLimitBundle\Exception\RateLimitException;
use PQstudio\RestUtilityBundle\Utility\ResponseMetadata;

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
        $meta = new ResponseMetadata();

        if($exception instanceof RateLimitException) {
            $meta->setStatusCode($exception->getStatusCode());
            $meta->setError($exception->getError());
            $meta->setErrorMessage($exception->getErrorMessage());
            $meta->setMoreInfo($exception->getMoreInfo());

            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $response->setContent($this->jmsSerializer->serialize($meta->build(), 'json'));

            $event->setResponse($response);
        }
    }
}
