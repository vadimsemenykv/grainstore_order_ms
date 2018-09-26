<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:00 PM
 */

namespace Service\Contract\Base;

use Assert\Assertion;
use Service\Contract\Contracts\ValueObject;
use Service\Contract\Exception\InvalidId;

class ObjectId implements ValueObject
{
    protected $id;

    public function toString(): string
    {
        return $this->id;
    }

    public static function fromString(string $id): ValueObject
    {
        return new self($id);
    }

    public function sameValueAs(ValueObject $object): bool
    {
        /** @var ObjectId $object */
        return \get_class($this) === \get_class($object) && $this->id === $object->id;
    }

    private function __construct($id)
    {
        try {
            Assertion::notEmpty($id);
        } catch (\Exception $e) {
            throw InvalidId::reason($e->getMessage());
        }

        $this->id = $id;
    }
}