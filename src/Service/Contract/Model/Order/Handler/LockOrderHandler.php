<?php

namespace Service\Contract\Model\Order\Handler;

use Service\Contract\Base\CommandHandler;
use Service\Contract\Model\Order\Command\LockOrder;
use Service\Contract\Model\Order\IOrderRepository;
use Service\Infrastructure\Messaging\Message\Base\Message;

/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/30/18
 * Time: 3:27 PM
 */

class LockOrderHandler extends CommandHandler
{
    /** @var IOrderRepository */
    private $orderRepository;

    /**
     * LockOrderHandler constructor.
     * @param IOrderRepository $orderRepository
     */
    public function __construct(IOrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param LockOrder|Message $message
     */
    public function invoke(Message $message)
    {
        $order = $this->orderRepository->get($message->orderId());
        $order->lock($message->lockBy());
        $this->orderRepository->save($order);
    }
}