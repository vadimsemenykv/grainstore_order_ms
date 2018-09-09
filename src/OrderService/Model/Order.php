<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:38 PM
 */

namespace Service\Order\Model;

use Service\Order\Base\AggregateRoot;
use Service\Order\Base\ApplyEventTrait;
use Service\Order\Model\Event\CreatedOrder;

final class Order extends AggregateRoot
{
    use ApplyEventTrait;

    /** @var OrderId */
    private $id;
    /** @var OwnerId */
    private $ownerId;

    /** @var \DateTime */
    private $created;
    /** @var \DateTime */
    private $updated;

    private function __construct(OrderId $id, OwnerId $ownerId)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
    }

    /**
     * @param OrderId $id
     * @param OwnerId $ownerId
     * @return Order
     */
    public static function create(OrderId $id, OwnerId $ownerId): Order
    {
        $self = new self($id, $ownerId);
        $self->recordThat(CreatedOrder::create($id, $ownerId));
        return $self;
    }

    protected function whenCreatedOrder(CreatedOrder $createdOrder): void
    {
        $this->id = $createdOrder->orderId();
        $this->ownerId = $createdOrder->ownerId();
    }

    public function update()
    {

    }

    public function changeStatus()
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