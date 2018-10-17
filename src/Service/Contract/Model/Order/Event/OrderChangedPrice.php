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
use Service\Contract\Model\Order\Price;
use Service\Infrastructure\Messaging\Message\DomainEvent;

class OrderChangedPrice extends DomainEvent
{
    /** @var Price */
    private $price;

    public static function create(OrderId $orderId, Price $price): OrderChangedPrice {
        try {
            $event = new self(
                Order::class,
                $orderId->toString(),
                ['price' => $price->toString()]
            );
            $event->price = $price;
            return $event;
        } catch (AssertionFailedException $exception) {
            throw CriticalError::wrap($exception);
        }
    }

    /**
     * @return Price
     */
    public function price(): Price
    {
        if (null === $this->price) {
            $this->price = Price::fromString($this->payload()['price']);
        }

        return $this->price;
    }
}