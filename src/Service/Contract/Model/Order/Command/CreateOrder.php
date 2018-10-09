<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/26/18
 * Time: 10:01 PM
 */

namespace Service\Contract\Model\Order\Command;

use Service\Contract\Model\Order\Id\CategoryCollectionId;
use Service\Contract\Model\Order\Id\CurrencyCollectionId;
use Service\Contract\Model\Order\Id\OrderId;
use Service\Contract\Model\Order\Id\OwnerId;
use Service\Contract\Model\Order\OfferOnly;
use Service\Contract\Model\Order\Price;
use Service\Contract\Model\Order\Quantity;
use Service\Infrastructure\Messaging\Message\Command;

class CreateOrder extends Command
{
    /**
     * @param string $orderId
     * @param string $ownerId
     * @param string $categoryCollectionId
     * @param string $currencyCollectionId
     * @param bool $offerOnly
     * @param float $price
     * @param int $quantity
     * @return CreateOrder
     *
     * @throws \Exception
     */
    public static function withData(
        string $orderId,
        string $ownerId,
        string $categoryCollectionId,
        string $currencyCollectionId,
        bool $offerOnly,
        float $price,
        int $quantity
    ) {
        $command = new self([
            'orderId' => $orderId,
            'ownerId' => $ownerId,
            'categoryCollectionId' => $categoryCollectionId,
            'currencyCollectionId' => $currencyCollectionId,
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

    public function ownerId(): OwnerId
    {
        /** @var OwnerId $ownerId */
        $ownerId = OwnerId::fromString($this->payload()['ownerId']);
        return $ownerId;
    }

    public function categoryCollectionId(): CategoryCollectionId
    {
        /** @var CategoryCollectionId $categoryCollectionId */
        $categoryCollectionId = CategoryCollectionId::fromString($this->payload()['categoryCollectionId']);
        return $categoryCollectionId;
    }

    public function currencyCollectionId(): CurrencyCollectionId
    {
        /** @var CurrencyCollectionId $currencyCollectionId */
        $currencyCollectionId = CurrencyCollectionId::fromString($this->payload()['currencyCollectionId']);
        return $currencyCollectionId;
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