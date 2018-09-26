<?php

namespace App\Infrastructure\Exception\Http;

use App\Infrastructure\Exception\AttributesContainer;
use App\Infrastructure\Exception\AttributesContainerTrait;
use App\Infrastructure\Exception\ErrorCodeHolder;
use App\Infrastructure\Exception\ErrorCodeHolderTrait;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException as SymfonyBadRequestHttpException;

class BadRequestHttpException extends SymfonyBadRequestHttpException implements AttributesContainer, ErrorCodeHolder
{
    use AttributesContainerTrait, ErrorCodeHolderTrait;
}
