<?php

namespace Service\Contract\Model\Order\Handler;

use Service\Contract\Base\CommandHandler;
use Service\Contract\Model\Order\Command\ChangeAttributes;
use Service\Contract\Model\Order\IOrderRepository;
use Service\Infrastructure\Messaging\Message\Base\Message;

/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/30/18
 * Time: 3:27 PM
 */

class ChangeAttributesHandler extends CommandHandler
{
    /** @var IOrderRepository */
    private $orderRepository;

    /**
     * ChangeAttributesHandler constructor.
     * @param IOrderRepository $orderRepository
     */
    public function __construct(IOrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param ChangeAttributes|Message $message
     */
    public function invoke(Message $message)
    {
        $order = $this->orderRepository->get($message->orderId());
        $order->changeOfferOnly($message->offerOnly(), $message->ownerId());
        $order->changePrice($message->price(), $message->ownerId());
        $order->changeQuantity($message->quantity(), $message->ownerId());
        $this->orderRepository->save($order);
    }
}