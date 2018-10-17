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
use Service\Contract\Model\Order\Id\UserId;
use Service\Contract\Model\Order\Order;
use Service\Infrastructure\Messaging\Message\DomainEvent;

class OrderLocked extends DomainEvent
{
    /** @var UserId */
    private $lockedBy;

    public static function create(OrderId $orderId, UserId $lockedBy): OrderLocked {
        try {
            $event = new self(
                Order::class,
                $orderId->toString(),
                ['lockedBy' => $lockedBy->toString()]
            );
            $event->lockedBy = $lockedBy;
            return $event;
        } catch (AssertionFailedException $exception) {
            throw CriticalError::wrap($exception);
        }
    }

    /**
     * @return UserId
     */
    public function lockedBy(): UserId
    {
        if (null === $this->lockedBy) {
            $this->lockedBy = UserId::fromString($this->payload()['lockedBy']);
        }

        return $this->lockedBy;
    }
}