<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 8:52 PM
 */

namespace Contract\Service\Exception;

class InvalidId extends \InvalidArgumentException
{
    public static function reason(string $msg): InvalidId
    {
        return new self('Invalid id because ' . $msg);
    }
}