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

class Status implements ValueObject
{
    public const ACTIVE = 'active';
    public const DEACTIVATED = 'deactivated';
    public const IN_CONTRACT = 'in_contract';

    protected $status;

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }

    public function inContract(): bool
    {
        return $this->status === self::IN_CONTRACT;
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
     * @return Status
     */
    public static function fromString(string $status): Status
    {
        return new self($status);
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        /** @var Status $object */
        return \get_class($this) === \get_class($object) && $this->status === $object->status;
    }

    private function __construct(string $status)
    {
        try {
            Assertion::inArray($status, [self::ACTIVE, self::DEACTIVATED]);
        } catch (\Exception $e) {
            throw InvalidStatus::reason($e->getMessage());
        }

        $this->status = $status;
    }
}