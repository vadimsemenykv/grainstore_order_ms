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
use Service\Infrastructure\Messaging\Message\DomainEvent;

class OrderUnLocked extends DomainEvent
{
    public static function create(OrderId $orderId): OrderUnLocked {
        try {
            $event = new self(
                Order::class,
                $orderId->toString(),
                ['lockedBy' => null]
            );
            return $event;
        } catch (AssertionFailedException $exception) {
            throw CriticalError::wrap($exception);
        }
    }
}