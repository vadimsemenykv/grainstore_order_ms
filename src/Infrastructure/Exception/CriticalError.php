<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:44 PM
 */

namespace App\Infrastructure\Exception;

class CriticalError  extends \RuntimeException
{
    /**
     * @param \Throwable $throwable
     * @return static
     */
    public static function wrap(\Throwable $throwable)
    {
        $throwable = new static($throwable->getMessage(), $throwable->getCode(), $throwable);
        return $throwable;
    }
}
