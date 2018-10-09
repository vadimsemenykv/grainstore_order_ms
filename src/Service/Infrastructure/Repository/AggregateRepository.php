<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 10/2/18
 * Time: 3:28 PM
 */

namespace Service\Infrastructure\Repository;

use Doctrine\MongoDB\Connection;
use MongoDB\Collection;
use Service\Contract\Base\AggregateRoot;
use Service\Contract\Model\Order\Order;
use Service\Infrastructure\Messaging\Bus\MessageBus;
use Service\Infrastructure\Messaging\Message\FQCNEvenFactory;

abstract class AggregateRepository extends EventSoreRepository
{
    /** @var Collection */
    private $collection;

    public function __construct(MessageBus $eventBus, Connection $connection, string $dbName, string $storeName)
    {
        parent::__construct($eventBus);
        $this->collection = $connection->selectCollection($dbName, $storeName);
    }


    public function saveAggregate(AggregateRoot $aggregateRoot)
    {
        foreach ($aggregateRoot->getPendingEvents() as $event) {
            $this->saveEvent($event);
        }
    }

    /**
     * @param string $id
     * @return null|AggregateRoot
     * @throws \Exception
     */
    public function getAggregate(string $id): ?AggregateRoot
    {
        $events = $this->collection->find(['aggregate_id' => $id])->sort(['_id' => 1]);
        $aggregate = $this->getCleanAggregateRoot();
        $factory = new FQCNEvenFactory();
        $count = 0;
        foreach ($events as $event) {
            $aggregate->apply($factory->createMessageFromArray($event['event_name'], $event));
            $count++;
        }

        if (empty($count)) {
            return null;
        }

        return $aggregate;
    }

    abstract protected function getCleanAggregateRoot(): AggregateRoot;
}