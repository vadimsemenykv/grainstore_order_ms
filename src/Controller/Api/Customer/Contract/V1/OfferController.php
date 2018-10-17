<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:27 PM
 */

namespace App\Controller\Api\Customer\Contract\V1;

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
    public function create(Request $request, RequestValidator $validator)
    {
        // Get payload
        $payload = $request->request->all();
        // Constraints
        $constraints = [
            'offer' => $this->constraintRequired([
                $this->constraintCollection([
                    'order' => $this->constraintRequired([
                        $this->constraintCollection([
                            'id' => $this->constraintType('string')
                        ])
                    ]),
                    'linked_orders' => $this->constraintOptional([
                        new Constraints\All([
                            $this->constraintCollection([
                                'id' => $this->constraintRequired([$this->constraintType('string')])
                            ])
                        ])
                    ]),
                    'linked_category_collection' => $this->constraintOptional([$this->constraintCollection(['id' => $this->constraintRequired()])]),
                    'price' => $this->constraintRequired([$this->constraintType('numeric')])
                ])
            ])
        ];
        $validator->validate($payload, $constraints);

        $orderId = $payload['offer']['order']['id'];
        $linkedOrders = $payload['offer']['linked_orders'];
        $linkedOrders = $payload['offer']['linked_category_collection']['id'] ?? null;
        $price = $payload['offer']['price'];

        $fromUserId = '2AD'; //TODO fetch from controller
//        $orderId = $
    }

    public function decline()
    {

    }
}