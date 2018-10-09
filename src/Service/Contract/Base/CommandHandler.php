<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/30/18
 * Time: 6:19 PM
 */

namespace Service\Contract\Base;

use Doctrine\Common\Persistence\ManagerRegistry;
use Service\Infrastructure\Messaging\Bus\MessageBus;
use Service\Infrastructure\Messaging\Message\Contract\Invokable;

abstract class CommandHandler implements Invokable
{
    //In future we will have some logic here

//    /** @var MessageBus */
//    protected $eventBus;
//
//    /**
//     * @param MessageBus $eventBus
//     */
//    public function __construct(MessageBus $eventBus)
//    {
//        $this->eventBus = $eventBus;
//    }
}