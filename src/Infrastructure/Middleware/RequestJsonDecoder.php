<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/25/18
 * Time: 8:54 PM
 */

namespace App\Infrastructure\Middleware;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestJsonDecoder
{
    /**
     * @param GetResponseEvent $event
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ("json" != $request->getContentType() || !$request->getContent()) {
            return;
        }
        $payload = \json_decode($request->getContent(), true);
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new BadRequestHttpException('Invalid JSON, maximum stack depth exceeded.');
            case JSON_ERROR_UTF8:
                throw new BadRequestHttpException('Malformed UTF-8 characters, possibly incorrectly encoded.');
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_STATE_MISMATCH:
                throw new BadRequestHttpException('Invalid JSON.');
        }
        $request->request->replace($payload);
    }
}