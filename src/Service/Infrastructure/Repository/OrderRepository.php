<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 10/2/18
 * Time: 3:02 PM
 */

namespace Service\Infrastructure\Repository;

use Service\Contract\Base\AggregateRoot;
use Service\Contract\Model\Order\Id\OrderId;
use Service\Contract\Model\Order\IOrderRepository;
use Service\Contract\Model\Order\Order;

class OrderRepository extends AggregateRepository implements IOrderRepository
{
    public function save(Order $order): void
    {
        $this->saveAggregate($order);
    }

    public function get(OrderId $orderId): ?Order
    {
        return $this->getAggregate($orderId->toString());
    }

    protected function getCleanAggregateRoot(): AggregateRoot
    {
        return new Order();
    }
}