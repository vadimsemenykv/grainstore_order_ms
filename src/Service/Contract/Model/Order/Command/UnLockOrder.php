<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/26/18
 * Time: 10:01 PM
 */

namespace Service\Contract\Model\Order\Command;

use Service\Contract\Model\Order\Id\OrderId;
use Service\Infrastructure\Messaging\Message\Command;

class UnLockOrder extends Command
{
    /**
     * @param string $orderId
     * @return UnLockOrder
     *
     * @throws \Exception
     */
    public static function make(
        string $orderId
    ): UnLockOrder {
        $command = new self([
            'orderId' => $orderId
        ]);

        return $command;
    }

    public function orderId(): OrderId
    {
        /** @var OrderId $orderId */
        $orderId = OrderId::fromString($this->payload()['orderId']);
        return $orderId;
    }
}