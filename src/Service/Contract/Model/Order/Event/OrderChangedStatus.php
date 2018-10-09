<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:36 PM
 */

namespace Service\Contract\Model\Order\Event;

use App\Infrastructure\Exception\CriticalError;
use Assert\AssertionFailedException;
use Service\Contract\Model\Order\Id\OrderId;
use Service\Contract\Model\Order\Order;
use Service\Contract\Model\Order\Status;
use Service\Infrastructure\Messaging\Message\DomainEvent;

class OrderChangedStatus extends DomainEvent
{
    /** @var Status */
    private $status;

    public static function create(OrderId $orderId, Status $status): OrderChangedStatus {
        try {
            $event = new self(
                Order::class,
                $orderId->toString(),
                ['status' => $status->toString()]
            );
            $event->status = $status;
            return $event;
        } catch (AssertionFailedException $exception) {
            throw CriticalError::wrap($exception);
        }
    }

    /**
     * @return Status
     */
    public function status(): Status
    {
        if (null === $this->status) {
            $this->status = Status::fromString($this->payload()['status']);
        }

        return $this->status;
    }
}