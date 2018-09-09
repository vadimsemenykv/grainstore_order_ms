<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:19 PM
 */

namespace App\Infrastructure\Messaging\Bus;

use App\Infrastructure\Messaging\Message\Base\Message;
use App\Infrastructure\Messaging\Message\Contract\Invokable;

class MessageBus
{
    protected $availableListeners;
    protected $listeners;
    /**
     * CommandBus constructor
     */
    public function __construct()
    {
        $this->availableListeners = [];
        $this->listeners = [];
    }
    public function dispatch(Message $message): void
    {
        /** @var Invokable $listenerHandler */
        foreach ($this->getListeners($message->messageName()) as $listenerHandler) {
            $listenerHandler->invoke($message);
            if ($message->propagationIsStopped()) {
                return;
            }
        }
    }
    /**
     * @param string $messageName
     * @return array|\Generator
     */
    public function getListeners(string $messageName)
    {
        $listeners = [];
        if (!empty($this->listeners[$messageName])) {
            $listeners[] = $this->listeners[$messageName];
        }
        if (!empty($this->listeners['.'])) {
            $listeners[] = $this->listeners['.'];
        }
        if (empty($listeners)) {
            return [];
        }
        foreach ($listeners as $listenerGroup) {
            /** @var Invokable[] $listenerGroup */
            foreach ($listenerGroup as $listener) {
                if (!$this->isListenerAvailable($listener)) {
                    continue;
                }
                yield $listener;
            }
            if (empty($listeners) && !empty($this->listeners['.'])) {
                $listeners = $this->listeners['.'];
            }
        }
    }
    /**
     * @param Invokable $listener
     * @param string $messageName - use '.' for listening all events
     * @param int $priority
     */
    public function attach(Invokable $listener, string $messageName = '.', int $priority = 1): void
    {
        $this->availableListeners[\get_class($listener)] = true;
        $this->listeners[$messageName][$priority] = $listener;
    }
    /**
     * @param Invokable $listener
     * @return bool
     */
    public function detach(Invokable $listener): bool
    {
        if (empty($this->availableListeners[\get_class($listener)])) {
            return false;
        }
        unset($this->availableListeners[\get_class($listener)]);
        return true;
    }
    /**
     * @param Invokable $listener
     * @return bool
     */
    public function disableListener(Invokable $listener): bool
    {
        if (empty($this->availableListeners[\get_class($listener)])) {
            return false;
        }
        $this->availableListeners[\get_class($listener)] = false;
        return true;
    }
    /**
     * @param Invokable $listener
     * @return bool
     */
    protected function isListenerAvailable(Invokable $listener): bool
    {
        return !empty($this->availableListeners[\get_class($listener)]);
    }
}
