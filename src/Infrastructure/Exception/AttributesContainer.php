<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

interface AttributesContainer
{
    public function addAttribute(Attribute $attribute);

    public function addAttributes(array $attribute);

    public function setAttributes(array $attributes);

    public function getAttributes() : array;

    public function toArray() : array;
}
