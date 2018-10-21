<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:38 PM
 */

namespace Service\Contract\Model\Order;

use Service\Contract\Base\AggregateRoot;
use Service\Contract\Base\ApplyEventTrait;
use Service\Contract\Model\Order\Event\OrderChangedOfferOnly;
use Service\Contract\Model\Order\Event\OrderChangedPrice;
use Service\Contract\Model\Order\Event\OrderChangedQuantity;
use Service\Contract\Model\Order\Event\OrderChangedStatus;
use Service\Contract\Model\Order\Event\OrderCreated;
use Service\Contract\Model\Order\Event\OrderLocked;
use Service\Contract\Model\Order\Event\OrderUnLocked;
use Service\Contract\Model\Order\Exception\FailedToGetLock;
use Service\Contract\Model\Order\Exception\InvalidOfferOnly;
use Service\Contract\Model\Order\Exception\InvalidPrice;
use Service\Contract\Model\Order\Exception\InvalidStatus;
use Service\Contract\Model\Order\Exception\TryingToModifyNotOwnedOrder;
use Service\Contract\Model\Order\Id\CategoryCollectionId;
use Service\Contract\Model\Order\Id\CurrencyCollectionId;
use Service\Contract\Model\Order\Id\OfferId;
use Service\Contract\Model\Order\Id\OrderId;
use Service\Contract\Model\Order\Id\UserId;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/**
 * Class Order
 * @package Service\Contracts\Model\Order
 *
 * @Document(collection="orders")
 */
final class Order extends AggregateRoot
{
    use ApplyEventTrait;

    /**
     * @Id
     */
    private $id;

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
    /** @var UserId */
    private $lockedBy;

    /** @var \DateTimeImmutable */
    private $created;
    /** @var \DateTime */
    private $updated;

    /**
     * @param OrderId $orderId
     * @param UserId $ownerId
     * @param CategoryCollectionId $categoryCollectionId
     * @param CurrencyCollectionId $currencyCollectionId
     * @param OfferOnly $offerOnly
     * @param Price $price
     * @param Quantity $quantity
     * @return Order
     */
    public static function create(
        OrderId $orderId,
        UserId $ownerId,
        CategoryCollectionId $categoryCollectionId,
        CurrencyCollectionId $currencyCollectionId,
        OfferOnly $offerOnly,
        Price $price,
        Quantity $quantity
    ): Order {
        $self = new self();
        $self->recordThat(OrderCreated::create(
            $orderId,
            $ownerId,
            $categoryCollectionId,
            $currencyCollectionId,
            $offerOnly,
            $price,
            $quantity,
            TotalPrice::from($price->price(), $quantity->quantity(), $offerOnly->isOfferType()),
            Status::fromString(Status::DEACTIVATED)
        ));
        return $self;
    }

    protected function whenOrderCreated(OrderCreated $createdOrder): void
    {
        $this->orderId = $createdOrder->orderId();
        $this->ownerId = $createdOrder->ownerId();
        $this->categoryCollectionId = $createdOrder->categoryCollectionId();
        $this->currencyCollectionId = $createdOrder->currencyCollectionId();
        $this->price = $createdOrder->price();
        $this->quantity = $createdOrder->quantity();
        $this->offerOnly = $createdOrder->offerOnly();
        $this->totalPrice = $createdOrder->totalPrice();
        $this->status = $createdOrder->status();

        $this->created = $createdOrder->created();
    }

    public function changeStatus(Status $status, UserId $byUserId): void
    {
        $this->validateOwner($byUserId);
        if ($status->sameValueAs($this->status)) {
            throw InvalidStatus::reason('order already have same status');
        }

        $this->recordThat(OrderChangedStatus::create($this->orderId, $status));
    }

    protected function whenOrderChangedStatus(OrderChangedStatus $changedStatus): void
    {
        $this->status = $changedStatus->status();
    }

    public function changePrice(Price $price, UserId $byUserId): void
    {
        $this->validateOwner($byUserId);
        if (!$price->isGreaterThanZero() && !$this->offerOnly->isOfferType()) {
            throw InvalidPrice::reason('price should be greater than 0');
        }
        $this->recordThat(OrderChangedPrice::create($this->orderId, $price));
    }

    protected function whenOrderChangedPrice(OrderChangedPrice $changedPrice): void
    {
        $this->price = $changedPrice->price();
    }

    public function changeOfferOnly(OfferOnly $offerOnly, UserId $byUserId): void
    {
        $this->validateOwner($byUserId);
        if (!$this->price->isGreaterThanZero() && !$offerOnly->isOfferType()) {
            throw InvalidOfferOnly::reason('price should be greater than 0');
        }
        $this->recordThat(OrderChangedOfferOnly::create($this->orderId, $offerOnly));
    }

    protected function whenOrderChangedOfferOnly(OrderChangedOfferOnly $changedOfferOnly): void
    {
        $this->offerOnly = $changedOfferOnly->offerOnly();
    }

    public function changeQuantity(Quantity $quantity, UserId $byUserId): void
    {
        $this->validateOwner($byUserId);
        $this->recordThat(OrderChangedQuantity::create($this->orderId, $quantity));
    }

    protected function whenOrderChangedQuantity(OrderChangedQuantity $changedQuantity): void
    {
        $this->quantity = $changedQuantity->quantity();
    }

    public function lock(UserId $byUserId): void
    {
        if ($this->lockedBy && !$this->lockedBy->sameValueAs($byUserId)) {
            throw FailedToGetLock::reason();
        }
        $this->recordThat(OrderLocked::create($this->orderId, $byUserId));
    }

    protected function whenOrderLocked(OrderLocked $orderLocked): void
    {
        $this->lockedBy = $orderLocked->lockedBy();
    }

    public function unLock(): void
    {
        if (!$this->lockedBy) {
            throw FailedToGetLock::reason('Failed to free lock: lock is empty');
        }
        if ($this->status->inContract()) {
            throw FailedToGetLock::reason('Failed to free lock: order is in contract');
        }
        $this->recordThat(OrderUnLocked::create($this->orderId));
    }

    protected function whenOrderUnLocked(OrderUnLocked $orderUnLocked): void
    {
        $this->lockedBy = null;
    }

    public function createOffer(
        OfferId $offerId,
        OrderId $orderId,
        UserId $fromUserId,
        UserId $toUserId,
        UserId $orderOwnerId,
        Price $price,
        array $linkedOrders,
        ?CategoryCollectionId $linkedCategoryId = null,
        ?OfferId $basedOnOfferId = null
    ) {
//        $orderId
//        $fromUserId
//        $toUserId
//        $orderOwnerId
//        $basedOnOfferId
//                $price

    }

    public function retractOffer()
    {

    }

    public function declineOffer()
    {

    }

    public function acceptOffer()
    {

    }

    public function createContract()
    {

    }

    public function createOfferGroup()
    {

    }

    /**
     * @param UserId $byUserId
     */
    private function validateOwner(UserId $byUserId): void
    {
        if (!$byUserId->sameValueAs($this->ownerId)) {
            throw new TryingToModifyNotOwnedOrder();
        }
    }
}