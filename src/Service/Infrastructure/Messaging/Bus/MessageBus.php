<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:19 PM
 */

namespace Service\Infrastructure\Messaging\Bus;

use Service\Infrastructure\Exception\Message\Bus\TransactionAlreadyExists;
use Service\Infrastructure\Messaging\Message\Base\Message;
use Service\Infrastructure\Messaging\Message\Contract\Invokable;

class MessageBus
{
    protected $regularScope;
    protected $transactionScope;
    protected $transaction;

    /** @var Message[] */
    protected $transactionMessages;

    /**
     * CommandBus constructor
     */
    public function __construct()
    {
        $this->regularScope = new Scope();
        $this->transactionScope= new Scope();
        $this->transaction = false;
        $this->transactionMessages = [];
    }

    public function dispatch(Message $message): void
    {
        if ($this->transaction) {
            $this->transactionMessages[] = $message;
        }
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
        $scope = $this->currentScope();
        $listeners = [];
        if (!empty($scope->listeners[$messageName])) {
            $listeners[] = $scope->listeners[$messageName];
        }
        if (!empty($scope->listeners['.'])) {
            $listeners[] = $scope->listeners['.'];
        }
        if (empty($listeners)) {
            return [];
        }
        foreach ($listeners as $listenerGroup) {
            /** @var Invokable[] $listenerGroup */
            foreach ($listenerGroup as $listenerPriorityStack) {
                foreach ($listenerPriorityStack as $listener) {
                    if (!$this->isListenerAvailable($listener)) {
                        continue;
                    }
                    yield $listener;
                }
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
        $scope = $this->currentScope();
        $scope->availableListeners[\get_class($listener)] = true;
        $scope->listeners[$messageName][$priority][] = $listener;
    }

    /**
     * @param Invokable $listener
     * @return bool
     */
    public function detach(Invokable $listener): bool
    {
        $scope = $this->currentScope();
        if (empty($scope->availableListeners[\get_class($listener)])) {
            return false;
        }
        unset($scope->availableListeners[\get_class($listener)]);
        return true;
    }

    /**
     * @param Invokable $listener
     * @return bool
     */
    public function disableListener(Invokable $listener): bool
    {
        $scope = $this->currentScope();
        if (empty($scope->availableListeners[\get_class($listener)])) {
            return false;
        }
        $scope->availableListeners[\get_class($listener)] = false;
        return true;
    }

    public function transactionBegin(): void
    {
        if ($this->transaction) {
            throw new TransactionAlreadyExists();
        }
        $this->transaction = true;
        $this->clearTransactionData();
    }

    public function transactionCommit(): void
    {
        $this->transaction = false;
        foreach ($this->transactionMessages as $message) {
            $this->dispatch($message);
        }
        $this->clearTransactionData();
    }

    public function transactionRollback(): void
    {
        $this->transaction = false;
        $this->clearTransactionData();
    }

    protected function clearTransactionData()
    {
        $this->transactionMessages = [];
        $this->transactionScope = new Scope();
    }

    protected function currentScope(): Scope
    {
        return ($this->transaction) ? $this->transactionScope : $this->regularScope;
    }

    /**
     * @param Invokable $listener
     * @return bool
     */
    protected function isListenerAvailable(Invokable $listener): bool
    {
        return !empty($this->currentScope()->availableListeners[\get_class($listener)]);
    }
}
