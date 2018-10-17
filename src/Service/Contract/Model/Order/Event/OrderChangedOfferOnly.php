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
use Service\Contract\Model\Order\OfferOnly;
use Service\Contract\Model\Order\Order;
use Service\Infrastructure\Messaging\Message\DomainEvent;

class OrderChangedOfferOnly extends DomainEvent
{
    /** @var OfferOnly */
    private $offerOnly;

    public static function create(OrderId $orderId, OfferOnly $offerOnly): OrderChangedOfferOnly {
        try {
            $event = new self(
                Order::class,
                $orderId->toString(),
                ['offerOnly' => $offerOnly->toString()]
            );
            $event->offerOnly = $offerOnly;
            return $event;
        } catch (AssertionFailedException $exception) {
            throw CriticalError::wrap($exception);
        }
    }

    /**
     * @return OfferOnly
     */
    public function offerOnly(): OfferOnly
    {
        if (null === $this->offerOnly) {
            $this->offerOnly = OfferOnly::fromString($this->payload()['offerOnly']);
        }

        return $this->offerOnly;
    }
}