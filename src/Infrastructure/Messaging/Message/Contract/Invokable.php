<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:22 PM
 */

namespace App\Infrastructure\Messaging\Message\Contract;

use App\Infrastructure\Messaging\Message\Base\Message;

interface Invokable
{
    public function invoke(Message $message);
}
