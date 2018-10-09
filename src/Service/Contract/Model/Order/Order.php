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
use Service\Contract\Model\Order\Event\OrderChangedStatus;
use Service\Contract\Model\Order\Event\OrderCreated;
use Service\Contract\Model\Order\Exception\InvalidStatus;
use Service\Contract\Model\Order\Exception\TryingToModifyNotOwnedOrder;
use Service\Contract\Model\Order\Id\CategoryCollectionId;
use Service\Contract\Model\Order\Id\CurrencyCollectionId;
use Service\Contract\Model\Order\Id\OrderId;
use Service\Contract\Model\Order\Id\OwnerId;
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
    /** @var OwnerId */
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
    /** @var AvailabilityStatus */
    private $availabilityStatus;

    /** @var \DateTimeImmutable */
    private $created;
    /** @var \DateTime */
    private $updated;

    /**
     * @param OrderId $orderId
     * @param OwnerId $ownerId
     * @param CategoryCollectionId $categoryCollectionId
     * @param CurrencyCollectionId $currencyCollectionId
     * @param OfferOnly $offerOnly
     * @param Price $price
     * @param Quantity $quantity
     * @return Order
     */
    public static function create(
        OrderId $orderId,
        OwnerId $ownerId,
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
            Status::fromString(Status::DEACTIVATED),
            AvailabilityStatus::fromString(AvailabilityStatus::AVAILABLE)
        ));
        return $self;
    }

    protected function whenCreatedOrder(OrderCreated $createdOrder): void
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
        $this->availabilityStatus = $createdOrder->availabilityStatus();

        $this->created = $createdOrder->created();
    }

    public function changeStatus(Status $status, OwnerId $byUserId): void
    {
        if ($status->sameValueAs($this->status)) {
            throw InvalidStatus::reason('order already have same status');
        }
        if (!$byUserId->sameValueAs($this->ownerId)) {
            throw new TryingToModifyNotOwnedOrder();
        }

        $this->recordThat(OrderChangedStatus::create($this->orderId, $status));
    }

    public function whenOrderChangedStatus(OrderChangedStatus $changedStatus): void
    {
        $this->status = $changedStatus->status();
    }

    public function update()
    {

    }

    public function reserve()
    {

    }

    public function releaseReservation()
    {

    }

    public function createOffer()
    {

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

    public function addOfferToGroup()
    {

    }
}