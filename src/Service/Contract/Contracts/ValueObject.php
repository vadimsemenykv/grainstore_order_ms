<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 8:33 PM
 */

namespace Service\Contract\Contracts;

interface ValueObject
{
    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool;
}