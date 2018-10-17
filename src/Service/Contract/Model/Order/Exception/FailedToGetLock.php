<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/26/18
 * Time: 10:44 PM
 */

namespace Service\Contract\Model\Order\Exception;


class FailedToGetLock extends \LogicException
{
    public static function reason(?string $msg = null): FailedToGetLock
    {
        if (!$msg) {
            $msg = 'Failed to get lock because';
        }
        return new self($msg);
    }
}