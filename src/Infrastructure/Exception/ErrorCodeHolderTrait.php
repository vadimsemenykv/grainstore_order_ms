<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

trait ErrorCodeHolderTrait
{
    private $errorCode;

    public function setErrorCode(string $errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    public function getErrorCode() : ?string
    {
        return $this->errorCode;
    }
}
