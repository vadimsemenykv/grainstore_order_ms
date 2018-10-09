<?php

namespace Service\Contract\Model\Order\Handler;

use Service\Contract\Base\CommandHandler;
use Service\Contract\Model\Order\Command\CreateOrder;
use Service\Contract\Model\Order\IOrderRepository;
use Service\Contract\Model\Order\Order;
use Service\Infrastructure\Messaging\Message\Base\Message;

/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/30/18
 * Time: 3:27 PM
 */

class CreateOrderHandler extends CommandHandler
{
    /** @var IOrderRepository */
    private $orderRepository;

    /**
     * CreateOrderHandler constructor.
     * @param IOrderRepository $orderRepository
     */
    public function __construct(IOrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param CreateOrder $message
     */
    public function invoke(Message $message)
    {
        $order = Order::create(
            $message->orderId(),
            $message->ownerId(),
            $message->categoryCollectionId(),
            $message->currencyCollectionId(),
            $message->offerOnly(),
            $message->price(),
            $message->quantity()
        );

        $this->orderRepository->save($order);
    }
}