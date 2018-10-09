<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/26/18
 * Time: 10:44 PM
 */

namespace Service\Contract\Model\Order\Exception;


class InvalidTotalPrice extends \InvalidArgumentException
{
    public static function reason(string $msg): InvalidTotalPrice
    {
        return new self('Invalid totalPrice because ' . $msg);
    }
}