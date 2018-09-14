<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 8:33 PM
 */

namespace Contract\Service\Contract;

interface ValueObject
{
    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @param string $objectString
     * @return ValueObject
     */
    public static function fromString(string $objectString): self;

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool;
}