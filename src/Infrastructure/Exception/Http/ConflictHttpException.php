<?php

namespace App\Infrastructure\Exception\Http;

use App\Infrastructure\Exception\AttributesContainer;
use App\Infrastructure\Exception\AttributesContainerTrait;
use App\Infrastructure\Exception\ErrorCodeHolder;
use App\Infrastructure\Exception\ErrorCodeHolderTrait;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException as SymfonyConflictHttpException;

class ConflictHttpException extends SymfonyConflictHttpException implements AttributesContainer, ErrorCodeHolder
{
    use AttributesContainerTrait, ErrorCodeHolderTrait;
}
