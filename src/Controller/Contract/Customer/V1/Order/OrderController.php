<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:27 PM
 */

namespace App\Controller\Contract\Customer\V1\Order;

use App\Controller\BaseController;
use App\Infrastructure\DB\AlphaNumericGenerator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Validator\Constraints;
use Service\Contract\Model\Order\Order;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Infrastructure\Request\Validator as RequestValidator;

class OrderController extends BaseController
{
    /**
     * @param Request $request
     * @param RequestValidator $validator
     * @param DocumentManager $documentManager
     * @return JsonResponse
     */
    public function create(Request $request, RequestValidator $validator, DocumentManager $documentManager): JsonResponse
    {
        $ownerId = '1AS'; //TODO fetch from ->getUser()->id();
        //TODO validate payload
        // Get payload
        $payload = $request->request->all();
        // Constraints
        $constraints = [
            'order' => $this->constraintRequired([
                $this->constraintCollection([
                    'category_collection' => $this->constraintRequired([new Constraints\Collection(['id' => $this->constraintRequired()])]),
                    'currency_collection' => $this->constraintRequired([new Constraints\Collection(['id' => $this->constraintRequired()])]),
                    'offer_only' => $this->constraintOptional([$this->constraintType('boolean')]),
                    'price' => $this->constraintOptional([$this->constraintType('numeric')]),
                    'quantity' => $this->constraintRequired([
                        $this->constraintGreaterThanOrEqual(1),
                        $this->constraintType('numeric')
                    ]),
                ])
            ])
        ];
        $validator->validate($payload, $constraints);

        $offerOnly = $payload['order']['offer_only'] ?? false;

        if (!$offerOnly) {
            $constraints = [
                'price' => $this->constraintOptional([
                    $this->constraintGreaterThanOrEqual(1),
                    $this->constraintType('numeric')
                ])
            ];
            $validator->validate(['price' => $payload['order']['price'] ?? false], $constraints);
        }

        $collectionId = $payload['order']['category_collection']['id'];
        $currencyId = $payload['order']['currency_collection']['id'];
        $price = $payload['order']['price'] ?? false;
        $quantity = $payload['order']['quantity'];
        $totalPrice = $offerOnly ? null : $price * $quantity;

        $orderId = $this->generateId($documentManager, 'orders');
        $command = new

        $documentManager->persist(new Order());
        $documentManager->flush();
        return $this->json(['id' => $orderId], 200);
    }

}