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
use Service\Contract\Model\Order\Status;
use Service\Infrastructure\Messaging\Message\Command;

class ChangeStatus extends Command
{
    /**
     * @param string $orderId
     * @param string $ownerId
     * @param string $status
     * @return ChangeStatus
     *
     * @throws \Exception
     */
    public static function make(
        string $orderId,
        string $ownerId,
        string $status
    ): ChangeStatus {
        $command = new self([
            'orderId' => $orderId,
            'ownerId' => $ownerId,
            'status' => $status
        ]);

        return $command;
    }

    public function orderId(): OrderId
    {
        /** @var OrderId $orderId */
        $orderId = OrderId::fromString($this->payload()['orderId']);
        return $orderId;
    }

    public function ownerId(): UserId
    {
        /** @var UserId $ownerId */
        $ownerId = UserId::fromString($this->payload()['ownerId']);
        return $ownerId;
    }

    public function status(): Status
    {
        /** @var Status $categoryCollectionId */
        $status = Status::fromString($this->payload()['status']);
        return $status;
    }
}