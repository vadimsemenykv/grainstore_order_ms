<?php

namespace Service\Contract\Model\Order\Handler;

use Service\Contract\Base\CommandHandler;
use Service\Contract\Model\Order\Command\ChangeStatus;
use Service\Contract\Model\Order\IOrderRepository;
use Service\Infrastructure\Messaging\Message\Base\Message;

/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/30/18
 * Time: 3:27 PM
 */

class ChangeStatusHandler extends CommandHandler
{
    /** @var IOrderRepository */
    private $orderRepository;

    /**
     * ChangeStatusHandler constructor.
     * @param IOrderRepository $orderRepository
     */
    public function __construct(IOrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param ChangeStatus|Message $message
     */
    public function invoke(Message $message)
    {
        $order = $this->orderRepository->get($message->orderId());
        $order->changeStatus($message->status(), $message->ownerId());
        $this->orderRepository->save($order);
    }
}