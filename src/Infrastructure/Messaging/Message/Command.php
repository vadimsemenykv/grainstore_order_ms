<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:27 PM
 */

namespace App\Infrastructure\Messaging\Message;

use App\Infrastructure\Messaging\Message\Base\Message;

class Command extends Message
{
    /**
     * Command constructor.
     * @param array $payload
     *
     * @throws \Exception
     */
    public function __construct(array $payload = [])
    {
        $this->init();
        $this->setPayload($payload);
    }

    /**
     * @inheritdoc
     */
    public function messageType(): string
    {
        return self::TYPE_COMMAND;
    }
}
