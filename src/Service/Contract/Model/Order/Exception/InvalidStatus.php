<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/26/18
 * Time: 10:44 PM
 */

namespace Service\Contract\Model\Order\Exception;


class InvalidStatus extends \InvalidArgumentException
{
    public static function reason(string $msg): InvalidStatus
    {
        return new self('Invalid status because ' . $msg);
    }
}