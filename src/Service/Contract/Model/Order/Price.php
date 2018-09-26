<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 8:30 PM
 */

namespace Service\Contract\Model\Order\Id;

use Assert\Assertion;
use Service\Contract\Contracts\ValueObject;
use Service\Contract\Model\Order\Exception\InvalidPrice;

class Price implements ValueObject
{
    protected $price;

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->price;
    }

    /**
     * @param string $price
     * @return ValueObject
     */
    public static function fromString(string $price): ValueObject
    {
        return new self((float)$price);
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        /** @var Price $object */
        return \get_class($this) === \get_class($object) && $this->price === $object->price;
    }

    private function __construct(float $price)
    {
        try {
            Assertion::notEmpty($price);
            Assertion::float($price);
        } catch (\Exception $e) {
            throw InvalidPrice::reason($e->getMessage());
        }

        $this->price = $price;
    }
}