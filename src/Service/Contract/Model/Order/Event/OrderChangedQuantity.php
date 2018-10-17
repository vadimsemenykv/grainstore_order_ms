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
use Service\Contract\Model\Order\Quantity;
use Service\Infrastructure\Messaging\Message\DomainEvent;

class OrderChangedQuantity extends DomainEvent
{
    /** @var Quantity */
    private $quantity;

    public static function create(OrderId $orderId, Quantity $quantity): OrderChangedQuantity {
        try {
            $event = new self(
                Order::class,
                $orderId->toString(),
                ['quantity' => $quantity->toString()]
            );
            $event->quantity = $quantity;
            return $event;
        } catch (AssertionFailedException $exception) {
            throw CriticalError::wrap($exception);
        }
    }

    /**
     * @return Quantity
     */
    public function quantity(): Quantity
    {
        if (null === $this->quantity) {
            $this->quantity = Quantity::fromString($this->payload()['quantity']);
        }

        return $this->quantity;
    }
}