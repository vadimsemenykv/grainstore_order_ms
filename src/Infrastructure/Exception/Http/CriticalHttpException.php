<?php

namespace App\Infrastructure\Exception\Http;

use App\Infrastructure\Exception\AttributesContainer;
use App\Infrastructure\Exception\AttributesContainerTrait;
use App\Infrastructure\Exception\ErrorCodeHolder;
use App\Infrastructure\Exception\ErrorCodeHolderTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CriticalHttpException extends HttpException implements AttributesContainer, ErrorCodeHolder
{
    use AttributesContainerTrait, ErrorCodeHolderTrait;

    public function __construct(
        string $message = null,
        \Exception $previous = null,
        ?int $code = 0,
        array $headers = array()
    ) {
        parent::__construct(500, $message, $previous, $headers, $code);
    }


}
