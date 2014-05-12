<?php
namespace PQstudio\RateLimitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Recaptcher\Exception\Exception;

class CaptchaController extends Controller
{
    public function checkCaptchaAction(Request $request)
    {
        $ip = $request->server->get('REMOTE_ADDR');

        $decoded = json_decode($request->getContent());
        $challenge = $decoded->challenge;
        $response = $decoded->response;

        $recaptcha = $this->get('recaptcha');
        $storage = $this->get('pq.rate_limit.storage.redis');

        try {
            if(true == $recaptcha->checkAnswer($ip, $challenge, $response)) {
                $storage->removeIpLimit($ip);
                return new JsonResponse(null, 200);
            }
        } catch(Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 422);
        }

        return new JsonResponse(null, 200);
    }
}
