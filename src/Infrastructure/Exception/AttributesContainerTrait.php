<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

trait AttributesContainerTrait
{
    private $attributes = [];

    /**
     * @param Attribute $attribute
     * @return self
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @param Attribute[] $attributes
     * @return self
     */
    public function addAttributes(array $attributes)
    {
        foreach ($attributes as $a) {
            $this->addAttribute($a);
        }

        return $this;
    }

    /**
     * @param Attribute[] $attributes
     * @return self
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = [];

        return $this->addAttributes($attributes);
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function toArray() : array
    {
        $result = [];

        /** @var Attribute $attribute */
        foreach ($this->attributes as $attribute) {
            $result[(string)$attribute->attributeName] = (string)$attribute->attributeVal;
        }

        return $result;
    }
}
