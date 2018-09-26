<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/26/18
 * Time: 10:44 PM
 */

namespace Service\Contract\Model\Order\Exception;


class InvalidPrice extends \InvalidArgumentException
{
    public static function reason(string $msg): InvalidPrice
    {
        return new self('Invalid price because ' . $msg);
    }
}