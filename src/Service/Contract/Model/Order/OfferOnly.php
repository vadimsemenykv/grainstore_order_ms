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
use Service\Contract\Model\Order\Exception\InvalidOfferOnly;

class OfferOnly implements ValueObject
{
    protected $isOnlyOffer;

    public function isOfferType(): bool
    {
        return $this->isOnlyOffer;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->isOnlyOffer;
    }

    /**
     * @param string $isOnlyOffer
     * @return OfferOnly
     */
    public static function fromString(string $isOnlyOffer): OfferOnly
    {
        return new self((bool)$isOnlyOffer);
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        /** @var OfferOnly $object */
        return \get_class($this) === \get_class($object) && $this->isOnlyOffer === $object->isOnlyOffer;
    }

    private function __construct(bool $isOnlyOffer)
    {
        try {
            Assertion::boolean($isOnlyOffer);
        } catch (\Exception $e) {
            throw InvalidOfferOnly::reason($e->getMessage());
        }

        $this->isOnlyOffer = $isOnlyOffer;
    }
}