<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 10/2/18
 * Time: 2:05 PM
 */

namespace Service\Infrastructure\Messaging\Bus;


class Scope
{
    public $listeners;
    public $availableListeners;

    public function __construct()
    {
        $this->listeners = [];
        $this->availableListeners = [];
    }
}