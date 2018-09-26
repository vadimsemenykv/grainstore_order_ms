<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

class Attribute
{
    public $attributeName;
    public $attributeVal;

    public function __construct($attributeName, $attributeVal)
    {
        $this->attributeName = $attributeName;
        $this->attributeVal = $attributeVal;
    }
}
