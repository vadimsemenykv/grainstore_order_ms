<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:27 PM
 */

namespace App\Controller\Api\Contract\Customer\V1\Order;

use App\Controller\BaseController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Service\Contract\Model\Order\Command\CreateOrder;
use Service\Contract\Model\Order\Id\OrderId;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;
use Service\Contract\Model\Order\Order;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Infrastructure\Request\Validator as RequestValidator;

class OfferController extends BaseController
{
    public function create()
    {

    }

    public function decline()
    {

    }
}