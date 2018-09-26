<?php declare(strict_types=1);

namespace App\Infrastructure\Middleware;

//use Monolog\Logger;
use App\Infrastructure\Exception\Attribute;
use App\Infrastructure\Exception\AttributesContainer;
use App\Infrastructure\Exception\ErrorCodeHolder;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionHandler
{
//    private $logger;
    private $isDebugEnabled;


//    public function __construct(LoggerInterface $logger, bool $isDebugEnabled)
    public function __construct(bool $isDebugEnabled)
    {
//        $this->logger = $logger;
        $this->isDebugEnabled = $isDebugEnabled;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        // Log exception
        $exceptionsStack = [];
        static::createExceptionsStack($exception, $exceptionsStack);
//        $context = [
//            'exceptions_stack' => array_reduce(
//                $exceptionsStack,
//                function($carry, $item) {
//                    $result = '';
//                    foreach ($item as $k => $v) {
//                        $result .= "$k: $v \n";
//                    }
//                    return $carry . $result."\n";
//                },
//                ''
//            ),
//        ];
//        $this->logger->log(
//            ($exception instanceof HttpExceptionInterface) ? Logger::ERROR : Logger::CRITICAL,
//            sprintf(
//                "Exception handled: %s at %s: %s, message: %s",
//                get_class($exception),
//                $exception->getFile(),
//                $exception->getLine(),
//                $exception->getMessage()
//            ),
//            $context
//        );

        // Create appropriate Response for this exception
        $response = new JsonResponse();
        $errors = [];
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->add($exception->getHeaders());
            if ($exception instanceof AttributesContainer && count($exception->getAttributes())) {
                /** @var Attribute $attribute */
                foreach ($exception->getAttributes() as $attribute) {
                    $error = [
                        "status" => (string)$exception->getStatusCode(),
                        "code" => $attribute->attributeVal,
                        "source" => ["pointer" => $attribute->attributeName,],
                    ];
                    $errors[] = $error;
                }
            } else {
                $error = [
                    "status" => (string)$exception->getStatusCode(),
                ];
                if ($exception instanceof ErrorCodeHolder && !empty($exception->getErrorCode())) {
                    $error["code"] = $exception->getErrorCode();
                } else {
                    $error["code"] = Response::$statusTexts[$exception->getStatusCode()];
                }
                $this->appendMeta($error, $exception);
                $errors[] = $error;
            }
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $error = [
                "status" => (string)Response::HTTP_INTERNAL_SERVER_ERROR,
                "code" => "internal_server_error",
            ];
            $this->appendMeta($error, $exception);
            $errors[] = $error;
        }
        $response->setData(["errors" => $errors]);
        $event->setResponse($response);
    }

    private function appendMeta(array &$error, \Throwable $exception)
    {
        if (!$this->isDebugEnabled) {
            return;
        }
        $exceptionsStack = [];
        static::createExceptionsStack($exception, $exceptionsStack);
        $error["meta"] = [
            "message" => $exception->getMessage(),
            "exception" => get_class($exception),
            "trace" => $exception->getTrace(),
            "exceptions_stack" => $exceptionsStack,
        ];
    }

    public static function createExceptionsStack(\Throwable $exception, array &$result)
    {
        $result[] = [
            'exception' => get_class($exception),
            'file' => "{$exception->getFile()}: {$exception->getLine()}",
            'message' => $exception->getMessage(),
        ];
        if (!empty($exception->getPrevious())) {
            static::createExceptionsStack($exception->getPrevious(), $result);
        }
    }
}