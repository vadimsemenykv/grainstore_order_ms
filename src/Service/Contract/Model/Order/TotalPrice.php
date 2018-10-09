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
use Service\Contract\Model\Order\Exception\InvalidTotalPrice;

class TotalPrice implements ValueObject
{
    protected $price;
    protected $quantity;
    protected $offerOnly;
    protected $totalPrice;

    /**
     * @return float
     */
    public function totalPrice(): float
    {
        return $this->totalPrice;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->totalPrice;
    }

    /**
     * @param float $price
     * @param int $quantity
     * @param bool $offerOnly
     * @return TotalPrice
     */
    public static function from(float $price, int $quantity, bool $offerOnly): TotalPrice
    {
        return new self($price, $quantity, $offerOnly);
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        /** @var TotalPrice $object */
        return \get_class($this) === \get_class($object)
            && $this->price === $object->price
            && $this->quantity === $object->quantity
            && $this->offerOnly === $object->offerOnly;
    }

    private function __construct(float $price, int $quantity, bool $offerOnly)
    {
        try {
            Assertion::float($price);
            Assertion::integer($quantity);
            Assertion::boolean($offerOnly);
            Assertion::greaterOrEqualThan($quantity, 1);
            if (!$offerOnly) {
                Assertion::greaterThan($price, 0);
            }
        } catch (\Exception $e) {
            throw InvalidTotalPrice::reason($e->getMessage());
        }

        $this->price = $price;
        $this->quantity = $quantity;
        $this->offerOnly = $offerOnly;
        $this->totalPrice = $offerOnly ? 0 : $quantity * $price;
    }
}