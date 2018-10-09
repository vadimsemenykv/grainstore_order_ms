<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/30/18
 * Time: 5:39 PM
 */

namespace Service\Contract\Model\Order;

use Assert\Assertion;
use Service\Contract\Contracts\ValueObject;
use Service\Contract\Model\Order\Exception\InvalidStatus;

class AvailabilityStatus implements ValueObject
{
    public const AVAILABLE = 'available';
    public const LOCKED = 'locked';
    public const IN_CONTRACT = 'in_contract';

    protected $status;

    /**
     * @return float
     */
    public function price(): float
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->status;
    }

    /**
     * @param string $status
     * @return AvailabilityStatus
     */
    public static function fromString(string $status): AvailabilityStatus
    {
        return new self($status);
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        /** @var AvailabilityStatus $object */
        return \get_class($this) === \get_class($object) && $this->status === $object->status;
    }

    private function __construct(string $status)
    {
        try {
            Assertion::inArray($status, [self::AVAILABLE, self::LOCKED, self::IN_CONTRACT]);
        } catch (\Exception $e) {
            throw InvalidStatus::reason($e->getMessage());
        }

        $this->status = $status;
    }
}