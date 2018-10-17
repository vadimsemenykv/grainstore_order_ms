<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/26/18
 * Time: 10:01 PM
 */

namespace Service\Contract\Model\Order\Command;

use Service\Contract\Model\Order\Id\OrderId;
use Service\Contract\Model\Order\Id\UserId;
use Service\Infrastructure\Messaging\Message\Command;

class LockOrder extends Command
{
    /**
     * @param string $orderId
     * @param string $lockBy
     * @return LockOrder
     *
     * @throws \Exception
     */
    public static function make(
        string $orderId,
        string $lockBy
    ): LockOrder {
        $command = new self([
            'orderId' => $orderId,
            'lockBy' => $lockBy
        ]);

        return $command;
    }

    public function orderId(): OrderId
    {
        /** @var OrderId $orderId */
        $orderId = OrderId::fromString($this->payload()['orderId']);
        return $orderId;
    }

    public function lockBy(): UserId
    {
        /** @var UserId $ownerId */
        $ownerId = UserId::fromString($this->payload()['lockBy']);
        return $ownerId;
    }
}