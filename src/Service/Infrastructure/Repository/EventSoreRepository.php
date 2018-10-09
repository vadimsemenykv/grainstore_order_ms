<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 10/2/18
 * Time: 3:17 PM
 */

namespace Service\Infrastructure\Repository;

use Service\Infrastructure\Messaging\Bus\MessageBus;
use Service\Infrastructure\Messaging\Message\Base\Message;
use Service\Infrastructure\Messaging\Message\DomainEvent;

class EventSoreRepository
{
    /** @var MessageBus */
    private $eventBus;

    public function __construct(MessageBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param DomainEvent|Message $message
     */
    public function saveEvent(Message $message)
    {
        $this->eventBus->dispatch($message);
    }
}