<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:36 PM
 */

namespace Service\Contract\Model\Order\Event;

use App\Infrastructure\Exception\CriticalError;
use Service\Contract\Model\Order\Order;
use Service\Contract\Model\Order\OfferOnly;
use Service\Contract\Model\Order\Price;
use Service\Contract\Model\Order\Quantity;
use Service\Contract\Model\Order\Id\OrderId;
use Service\Contract\Model\Order\Id\UserId;
use Service\Contract\Model\Order\Id\CategoryCollectionId;
use Service\Contract\Model\Order\Id\CurrencyCollectionId;
use Service\Contract\Model\Order\Status;
use Service\Contract\Model\Order\TotalPrice;
use Service\Infrastructure\Messaging\Message\DomainEvent;
use Assert\AssertionFailedException;

class OrderCreated extends DomainEvent
{
    /** @var OrderId */
    private $orderId;
    /** @var UserId */
    private $ownerId;
    /** @var CategoryCollectionId */
    private $categoryCollectionId;
    /** @var CurrencyCollectionId */
    private $currencyCollectionId;
    /** @var OfferOnly */
    private $offerOnly;
    /** @var Price */
    private $price;
    /** @var Quantity */
    private $quantity;
    /** @var TotalPrice */
    private $totalPrice;
    /** @var Status */
    private $status;

    public static function create(
        OrderId $orderId,
        UserId $ownerId,
        CategoryCollectionId $categoryCollectionId,
        CurrencyCollectionId $currencyCollectionId,
        OfferOnly $offerOnly,
        Price $price,
        Quantity $quantity,
        TotalPrice $totalPrice,
        Status $status
    ): OrderCreated {
        try {
            $event = new self(
                Order::class,
                $orderId->toString(),
                [
                    'orderId' => $orderId->toString(),
                    'ownerId' => $ownerId->toString(),
                    'categoryCollectionId' => $categoryCollectionId->toString(),
                    'currencyCollectionId' => $currencyCollectionId->toString(),
                    'price' => $price->toString(),
                    'quantity' => $quantity->toString(),
                    'offerOnly' => $offerOnly->toString(),
                    'totalPrice' => $totalPrice->toString(),
                    'status' => $status->toString()
                ]
            );
            $event->orderId = $orderId;
            $event->ownerId = $ownerId;
            $event->categoryCollectionId = $categoryCollectionId;
            $event->currencyCollectionId = $currencyCollectionId;
            $event->price = $price;
            $event->quantity = $quantity;
            $event->offerOnly = $offerOnly;
            $event->totalPrice = $totalPrice;
            $event->status = $status;
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
     * @return UserId
     */
    public function ownerId(): UserId
    {
        if (null === $this->ownerId) {
            $this->ownerId = UserId::fromString($this->payload()['ownerId']);
        }

        return $this->ownerId;
    }

    /**
     * @return CategoryCollectionId
     */
    public function categoryCollectionId(): CategoryCollectionId
    {
        if (null === $this->categoryCollectionId) {
            $this->categoryCollectionId = CategoryCollectionId::fromString($this->payload()['categoryCollectionId']);
        }

        return $this->categoryCollectionId;
    }

    /**
     * @return CurrencyCollectionId
     */
    public function currencyCollectionId(): CurrencyCollectionId
    {
        if (null === $this->currencyCollectionId) {
            $this->currencyCollectionId = CurrencyCollectionId::fromString($this->payload()['currencyCollectionId']);
        }

        return $this->currencyCollectionId;
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

    /**
     * @return TotalPrice
     */
    public function totalPrice(): TotalPrice
    {
        if (null === $this->totalPrice) {
            $this->totalPrice = TotalPrice::from(
                (float)$this->payload()['price'],
                (int)$this->payload()['quantity'],
                (bool)$this->payload()['offerOnly']
            );
        }

        return $this->totalPrice;
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