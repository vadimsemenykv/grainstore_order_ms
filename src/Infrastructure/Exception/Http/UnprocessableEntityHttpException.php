<?php

namespace App\Infrastructure\Exception\Http;

use App\Infrastructure\Exception\AttributesContainer;
use App\Infrastructure\Exception\AttributesContainerTrait;
use App\Infrastructure\Exception\ErrorCodeHolder;
use App\Infrastructure\Exception\ErrorCodeHolderTrait;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException as SymfonyUnprocessableEntityHttpException;

class UnprocessableEntityHttpException extends SymfonyUnprocessableEntityHttpException implements
    AttributesContainer,
    ErrorCodeHolder
{
    use AttributesContainerTrait, ErrorCodeHolderTrait;
}
