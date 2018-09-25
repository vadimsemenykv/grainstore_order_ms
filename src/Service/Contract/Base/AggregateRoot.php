<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:09 PM
 */

namespace Service\Contract\Base;

use App\Infrastructure\Messaging\Message\DomainEvent;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private $pendingEvents = [];
    /**
     * @return DomainEvent[]|\Generator
     */
    public function getPendingEvents()
    {
        while (!empty($this->pendingEvents)) {
            yield array_pop($this->pendingEvents);
        }
    }
    /**
     * @param DomainEvent $event
     */
    protected function recordThat(DomainEvent $event): void
    {
        $this->apply($event);
        $this->pendingEvents[] = $event;
    }
    abstract protected function apply(DomainEvent $event): void;
}