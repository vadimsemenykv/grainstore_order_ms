<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:11 PM
 */

namespace Service\Contract\Base;

use Service\Infrastructure\Messaging\Message\DomainEvent;

trait ApplyEventTrait
{
    /**
     * @param DomainEvent $event
     * @throws \RuntimeException
     */
    public function apply(DomainEvent $event): void
    {
        $handler = $this->determineEventHandlerMethodFor($event);
        if (! method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                \get_class($this)
            ));
        }
        $this->{$handler}($event);
        if (property_exists(\get_class($this), 'updated')) {
            $this->updated = $event->created();
        }
    }

    protected function determineEventHandlerMethodFor(DomainEvent $event): string
    {
        return 'when' . implode(\array_slice(explode('\\', \get_class($event)), -1));
    }
}