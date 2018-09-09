<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:36 PM
 */

namespace Service\Order\Model\Event;

use App\Infrastructure\Exception\CriticalError;
use App\Infrastructure\Messaging\Message\DomainEvent;
use Assert\AssertionFailedException;
use Service\Order\Model\Order;
use Service\Order\Model\OrderId;
use Service\Order\Model\OwnerId;

class CreatedOrder extends DomainEvent
{
    /** @var OrderId */
    private $orderId;
    /** @var OwnerId */
    private $ownerId;

    public static function create(OrderId $orderId, OwnerId $ownerId): CreatedOrder
    {
        try {
            $event = new self(
                Order::class,
                $orderId->toString(),
                ['ownerId' => $ownerId->toString()]
            );
            $event->orderId = $orderId;
            $event->ownerId = $ownerId;
            return $event;
        } catch (AssertionFailedException $exception) {
            throw CriticalError::wrap($exception);
        }
    }

    /**
     * @return OrderId
     */
    public function orderId(): OrderId
    {
        if (null === $this->orderId) {
            $this->orderId = OrderId::fromString($this->aggregateId());
        }

        return $this->orderId;
    }

    /**
     * @return OwnerId
     */
    public function ownerId(): OwnerId
    {
        if (null === $this->ownerId) {
            $this->ownerId = OwnerId::fromString($this->payload()['ownerId']);
        }

        return $this->ownerId;
    }
}