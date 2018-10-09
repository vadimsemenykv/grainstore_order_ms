<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 10/2/18
 * Time: 2:14 PM
 */

namespace Service\Infrastructure\Listeners;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Id\IncrementGenerator;
use Service\Contract\Model\Order\Order;
use Service\Infrastructure\Messaging\Message\Base\Message;
use Service\Infrastructure\Messaging\Message\Contract\Invokable;
use Service\Infrastructure\Messaging\Message\DomainEvent;

class EventStoreWriter implements Invokable
{
    /** @var string */
    private $dbName;
    /** @var string */
    private $storeName;
    /** @var DocumentManager */
    private $documentManager;

    public function __construct(DocumentManager $documentManager, string $dbName, string $storeName)
    {
        $this->documentManager = $documentManager;
        $this->dbName = $dbName;
        $this->storeName = $storeName;
    }

    /**
     * @param DomainEvent|Message $message
     */
    public function invoke(Message $message)
    {
        $generator = new IncrementGenerator();
        $generator->setKey('event_store');
        $event = [
            '_id' => $generator->generate($this->documentManager, new Order()),
            'event_uuid' => $message->uuid()->toString(),
            'event_name' => $message->messageName(),
            'payload' => $message->payload(),
            'metadata' => $message->metadata(),
            'created' => new \MongoDate($message->created()->getTimestamp()),
            'aggregate_id' => $message->aggregateId(),
            'aggregate_name' => $message->aggregateName()
        ];
        $this->documentManager->getConnection()->selectCollection($this->dbName, $this->storeName)
            ->insert($event);
    }
}