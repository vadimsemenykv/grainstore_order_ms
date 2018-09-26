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
use Service\Contract\Model\Order\Id\Price;
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
    public static function withData(string $orderId, string $ownerId, string $categoryCollectionId, string $currencyCollectionId, bool $offerOnly, float $price, int $quantity)
    {
        $command = new self([
            'order_id' => $orderId,
            'owner_id' => $ownerId,
            'category_collection_id' => $categoryCollectionId,
            'currency_collection_id' => $currencyCollectionId,
            'offer_only' => $offerOnly,
            'price' => $price,
            'quantity' => $quantity
        ]);

        return $command;
    }

    public function orderId(): OrderId
    {
        /** @var OrderId $orderId */
        $orderId = OrderId::fromString($this->payload()['order_id']);
        return $orderId;
    }

    public function ownerId(): OwnerId
    {
        /** @var OwnerId $ownerId */
        $ownerId = OwnerId::fromString($this->payload()['owner_id']);
        return $ownerId;
    }

    public function categoryCollectionId(): CategoryCollectionId
    {
        /** @var CategoryCollectionId $categoryCollectionId */
        $categoryCollectionId = CategoryCollectionId::fromString($this->payload()['category_collection_id']);
        return $categoryCollectionId;
    }

    public function currencyCollectionId(): CurrencyCollectionId
    {
        /** @var CurrencyCollectionId $currencyCollectionId */
        $currencyCollectionId = CurrencyCollectionId::fromString($this->payload()['currency_collection_id']);
        return $currencyCollectionId;
    }

    public function price(): Price
    {
        /** @var CurrencyCollectionId $currencyCollectionId */
        $currencyCollectionId = CurrencyCollectionId::fromString($this->payload()['price']);
        return $currencyCollectionId;
    }
}