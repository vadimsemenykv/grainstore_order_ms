<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 8:30 PM
 */

namespace Service\Contract\Model\Order;

use Assert\Assertion;
use Service\Contract\Contracts\ValueObject;
use Service\Contract\Model\Order\Exception\InvalidQuantity;

class Quantity implements ValueObject
{
    protected $quantity;

    /**
     * @return int
     */
    public function quantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->quantity;
    }

    /**
     * @param string $quantity
     * @return Quantity
     */
    public static function fromString(string $quantity): Quantity
    {
        return new self((int)$quantity);
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        /** @var Quantity $object */
        return \get_class($this) === \get_class($object) && $this->quantity === $object->quantity;
    }

    private function __construct(int $quantity)
    {
        try {
            Assertion::notEmpty($quantity);
            Assertion::integer($quantity);
        } catch (\Exception $e) {
            throw InvalidQuantity::reason($e->getMessage());
        }

        $this->quantity = $quantity;
    }
}