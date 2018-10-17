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
use Service\Contract\Model\Order\OfferOnly;
use Service\Contract\Model\Order\Price;
use Service\Contract\Model\Order\Quantity;
use Service\Contract\Model\Order\Status;
use Service\Infrastructure\Messaging\Message\Command;

class ChangeAttributes extends Command
{
    /**
     * @param string $orderId
     * @param string $ownerId
     * @param bool $offerOnly
     * @param float $price
     * @param int $quantity
     * @return ChangeAttributes
     *
     * @throws \Exception
     */
    public static function make(
        string $orderId,
        string $ownerId,
        bool $offerOnly,
        float $price,
        int $quantity
    ): ChangeAttributes {
        $command = new self([
            'orderId' => $orderId,
            'ownerId' => $ownerId,
            'offerOnly' => $offerOnly,
            'price' => $price,
            'quantity' => $quantity
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

    public function price(): Price
    {
        return Price::fromString($this->payload()['price']);
    }

    public function quantity(): Quantity
    {
        return Quantity::fromString($this->payload()['quantity']);
    }

    public function offerOnly(): OfferOnly
    {
        return OfferOnly::fromString($this->payload()['offerOnly']);
    }
}