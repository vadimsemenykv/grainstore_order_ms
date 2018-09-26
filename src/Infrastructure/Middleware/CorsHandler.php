<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/26/18
 * Time: 8:26 PM
 */

namespace App\Infrastructure\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CorsHandler
{
    /**
     * @param GetResponseEvent $event
     * @return void
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (Request::METHOD_OPTIONS !== $request->getMethod()) {
            return;
        }
        $options = ['GET', 'POST', 'PATCH', 'DELETE', 'OPTIONS'];
        $headers = ['Origin', 'Authorization', 'Content-Type'];
        $response = new Response('', 200);
        $response->headers->set('Allow', implode(', ', $options));
        $response->headers->set('Access-Control-Allow-Methods', implode(', ', $options));
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', $headers));
        $event->setResponse($response);
    }
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        // add CORS response headers
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
    }
}