<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 10/2/18
 * Time: 5:44 PM
 */

namespace Service\Infrastructure\Messaging\Message;

use DateTimeImmutable;
use DateTimeZone;

class FQCNEvenFactory
{
    /**
     * @param string $messageName
     * @param array $messageData
     * @return DomainEvent
     * @throws \Exception
     */
    public function createMessageFromArray(string $messageName, array $messageData): DomainEvent
    {
        if (!class_exists($messageName)) {
            throw new \UnexpectedValueException('Given message name is not a valid class: ' . (string) $messageName);
        }
        if (!is_subclass_of($messageName, DomainEvent::class)) {
            throw new \UnexpectedValueException(sprintf(
                'Message class %s is not a sub class of %s',
                $messageName,
                DomainEvent::class
            ));
        }
        $messageData['message_name'] = $messageName;
        $messageData['uuid'] = $messageData['event_uuid'];
        $messageData['created'] = DateTimeImmutable::createFromMutable($messageData['created']->toDateTime());

        if (!isset($messageData['metadata'])) {
            $messageData['metadata'] = [];
        }
        return $messageName::fromArray($messageData);
    }
}