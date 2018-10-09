<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 10/2/18
 * Time: 3:00 PM
 */

namespace Service\Contract\Model\Order;

use Service\Contract\Model\Order\Id\OrderId;

interface IOrderRepository
{
    public function save(Order $order): void;
    public function get(OrderId $orderId): ?Order;
}