<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:23 PM
 */

namespace Service\Infrastructure\Messaging\Message\Base;

use Assert\Assertion;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;

abstract class Message
{
    public const TYPE_COMMAND = 'command';
    public const TYPE_EVENT = 'event';

    /** @var string */
    protected $messageName;
    /** @var UuidInterface */
    protected $uuid;
    /** @var array */
    protected $payload = [];
    /** @var DateTimeImmutable */
    protected $created;
    /** @var array */
    protected $metadata = [];
    /** @var bool */
    protected $propagation = true;

    /**
     * @throws \Exception
     */
    protected function init(): void
    {
        if ($this->uuid === null) {
            $this->uuid = Uuid::uuid4();
        }
        if ($this->messageName === null) {
            $this->messageName = get_class($this);
        }
        if ($this->created === null) {
            $this->created = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        }
    }
    /**
     * Should be one of Message::TYPE_COMMAND, Message::TYPE_EVENT or Message::TYPE_QUERY
     */
    abstract public function messageType(): string;

    public function uuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function messageName(): string
    {
        return $this->messageName;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function created(): DateTimeImmutable
    {
        return $this->created;
    }

    public function toArray(): array
    {
        return [
            'message_name' => $this->messageName,
            'uuid' => $this->uuid->toString(),
            'payload' => $this->payload(),
            'metadata' => $this->metadata,
            'created' => $this->created(),
        ];
    }

    public function stopPropagation(): void
    {
        $this->propagation = false;
    }

    /**
     * @return bool
     */
    public function propagationIsStopped(): bool
    {
        return !$this->propagation;
    }

    /**
     * This method is called when message is instantiated
     * @param array $payload
     */
    protected function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @param array $messageData
     * @return Message
     * @throws \Assert\AssertionFailedException
     * @throws \ReflectionException
     */
    public static function fromArray(array $messageData): Message
    {
        Assertion::isArray($messageData, 'MessageData must be an array');
        Assertion::keyExists($messageData, 'message_name', 'MessageData must contain a key message_name');
        Assertion::keyExists($messageData, 'uuid', 'MessageData must contain a key uuid');
        Assertion::keyExists($messageData, 'payload', 'MessageData must contain a key payload');
        Assertion::keyExists($messageData, 'metadata', 'MessageData must contain a key metadata');
        Assertion::keyExists($messageData, 'created', 'MessageData must contain a key created');

        $messageRef = new ReflectionClass(get_called_class());
        /** @var $message Message */
        $message = $messageRef->newInstanceWithoutConstructor();
        $message->uuid = Uuid::fromString($messageData['uuid']);
        $message->messageName = $messageData['message_name'];
        $message->metadata = $messageData['metadata'];
        $message->created = $messageData['created'];
        $message->setPayload($messageData['payload']);

        return $message;
    }
}
