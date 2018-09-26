<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

interface ErrorCodeHolder
{
    public function setErrorCode(string $errorCode);

    public function getErrorCode() : ?string;
}
