<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:38 PM
 */

namespace Contract\Service\Order\Model;

use Contract\Service\Base\AggregateRoot;
use Contract\Service\Base\ApplyEventTrait;
use Contract\Service\Order\Model\Event\CreatedOrder;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/**
 * Class Order
 * @package Contract\Service\Order\Model
 *
 * @Document(collection="orders")
 */
final class Order extends AggregateRoot
{
    use ApplyEventTrait;

    /**
     * @Id(strategy="CUSTOM", type="string", options={"class"="App\Infrastructure\DB\AlphaNumericGenerator"}))
     */
    private $id;

//    /** @var OrderId */
//    private $id;
    /** @var OwnerId */
    private $ownerId;

    /** @var \DateTime */
    private $created;
    /** @var \DateTime */
    private $updated;

    /**
     * @param OrderId $id
     * @param OwnerId $ownerId
     * @return Order
     */
    public static function create(OrderId $id, OwnerId $ownerId): Order
    {
        $self = new self();
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