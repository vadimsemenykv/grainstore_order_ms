<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:27 PM
 */

namespace App\Controller\Api\Customer\Contract\V1;

use App\Controller\BaseController;
use App\Infrastructure\Exception\Attribute;
use App\Infrastructure\Exception\Http\BadRequestHttpException;
use App\Infrastructure\Request\Validator as RequestValidator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Service\Contract\Model\Order\Command\ChangeAttributes;
use Service\Contract\Model\Order\Command\ChangeStatus;
use Service\Contract\Model\Order\Command\CreateOrder;
use Service\Contract\Model\Order\Command\LockOrder;
use Service\Contract\Model\Order\Exception\FailedToGetLock;
use Service\Contract\Model\Order\Exception\InvalidOfferOnly;
use Service\Contract\Model\Order\Exception\InvalidPrice;
use Service\Contract\Model\Order\Exception\InvalidQuantity;
use Service\Contract\Model\Order\Exception\InvalidStatus;
use Service\Contract\Model\Order\Exception\TryingToModifyNotOwnedOrder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Validator\Constraints;

class OrderController extends BaseController
{
    /**
     * @param Request $request
     * @param RequestValidator $validator
     * @param DocumentManager $documentManager
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function create(Request $request, RequestValidator $validator, DocumentManager $documentManager): JsonResponse
    {
//        $this->get('eventStore\OrderRepository')->get(OrderId::fromString('135TS'));
//        $documentManager->persist(new Order());
//        $documentManager->flush();

        $userId = '1AS'; //TODO fetch from ->getUser()->id();
        // Get payload
        $payload = $request->request->all();
        // Constraints
        $constraints = [
            'order' => $this->constraintRequired([
                $this->constraintCollection([
                    'category_collection' => $this->constraintRequired([new Constraints\Collection(['id' => $this->constraintRequired()])]),
                    'currency_collection' => $this->constraintRequired([new Constraints\Collection(['id' => $this->constraintRequired()])]),
                    'offer_only' => $this->constraintRequired([$this->constraintType('boolean')]),
                    'price' => $this->constraintRequired([$this->constraintType('numeric')]),
                    'quantity' => $this->constraintRequired([
                        $this->constraintGreaterThanOrEqual(1),
                        $this->constraintType('numeric')
                    ]),
                ])
            ])
        ];
        $validator->validate($payload, $constraints);

        $offerOnly = $payload['order']['offer_only'];

        if (!$offerOnly) {
            $constraints = [
                'price' => $this->constraintRequired([
                    $this->constraintGreaterThanOrEqual(1),
                    $this->constraintType('numeric')
                ])
            ];
            $validator->validate(['price' => $payload['order']['price']], $constraints);
        }

        $categoryCollectionId = $payload['order']['category_collection']['id'];
        $currencyCollectionId = $payload['order']['currency_collection']['id'];
        $price = $payload['order']['price'] ?? 0;
        $quantity = $payload['order']['quantity'];

        $orderId = $this->generateId($documentManager, 'orders');

        $command = CreateOrder::withData(
            $orderId,
            $userId,
            $categoryCollectionId,
            $currencyCollectionId,
            $offerOnly,
            (float)$price,
            (int)$quantity
        );

        //TODO handle errors
        $this->get('contract_service_command_bus')->dispatch($command);
        return $this->json(['id' => $orderId], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param RequestValidator $validator
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function changeStatus(Request $request, RequestValidator $validator)
    {
        // Get payload
        $payload = $request->request->all();
        // Constraints
        $constraints = [
            'order' => $this->constraintRequired([
                $this->constraintCollection([
                    'id' => $this->constraintRequired([$this->constraintType('string')]),
                    'status' => $this->constraintRequired([$this->constraintType('string')]),
                ])
            ])
        ];
        $validator->validate($payload, $constraints);

        $orderId = $payload['order']['id'];
        $userId = '1AS'; //TODO fetch from ->getUser()->id();
        $status = $payload['order']['status'];

        $command = ChangeStatus::make($orderId, $userId, $status);
        try {
            $this->get('contract_service_command_bus')->dispatch($command);
        } catch (TryingToModifyNotOwnedOrder $e) {
            throw new UnauthorizedHttpException('Challenge');
        } catch (InvalidStatus $e) {
            throw (new BadRequestHttpException($e->getMessage()))
                ->setAttributes([new Attribute('order.status', $status)]);
        }
        return $this->json(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param RequestValidator $validator
     * @return JsonResponse
     * @throws \Exception
     */
    public function change(Request $request, RequestValidator $validator)
    {
        // Get payload
        $payload = $request->request->all();
        // Constraints
        $constraints = [
            'order' => $this->constraintRequired([
                $this->constraintCollection([
                    'id' => $this->constraintRequired([$this->constraintType('string')]),
                    'offer_only' => $this->constraintRequired([$this->constraintType('boolean')]),
                    'price' => $this->constraintRequired([$this->constraintType('numeric')]),
                    'quantity' => $this->constraintRequired([
                        $this->constraintGreaterThanOrEqual(1),
                        $this->constraintType('numeric')
                    ]),
                ])
            ])
        ];
        $validator->validate($payload, $constraints);

        $orderId = $payload['order']['id'];
        $userId = '1AS'; //TODO fetch from ->getUser()->id();
        $price = $payload['order']['price'];
        $quantity = $payload['order']['quantity'];
        $offerOnly = $payload['order']['offer_only'];

        if (!$offerOnly) {
            $constraints = [
                'price' => $this->constraintRequired([
                    $this->constraintGreaterThanOrEqual(1),
                    $this->constraintType('numeric')
                ])
            ];
            $validator->validate(['price' => $payload['order']['price']], $constraints);
        }

        $command = ChangeAttributes::make($orderId, $userId, $offerOnly, $price, $quantity);
        try {
            $this->get('contract_service_command_bus')->dispatch($command);
        } catch (TryingToModifyNotOwnedOrder $e) {
            throw new UnauthorizedHttpException('Challenge');
        } catch (InvalidPrice | InvalidOfferOnly $e) {
            throw (new BadRequestHttpException($e->getMessage()))
                ->setAttributes([new Attribute('order.price', $price)]);
        } catch (InvalidQuantity $e) {
            throw (new BadRequestHttpException($e->getMessage()))
                ->setAttributes([new Attribute('order.quantity', $quantity)]);
        }
        return $this->json(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param RequestValidator $validator
     * @return JsonResponse
     * @throws \Exception
     */
    public function lock(Request $request, RequestValidator $validator)
    {
        // Get payload
        $payload = $request->request->all();
        // Constraints
        $constraints = [
            'order' => $this->constraintRequired([
                $this->constraintCollection([
                    'id' => $this->constraintRequired([$this->constraintType('string')])
                ])
            ])
        ];
        $validator->validate($payload, $constraints);

        $orderId = $payload['order']['id'];
        $userId = '1AS'; //TODO fetch from ->getUser()->id();

        $command = LockOrder::make($orderId, $userId);
        try {
            $this->get('contract_service_command_bus')->dispatch($command);
        } catch (FailedToGetLock $e) {
            throw (new BadRequestHttpException($e->getMessage()))
                ->setAttributes([new Attribute('order.lock', $orderId)]);
        }
        return $this->json(null, Response::HTTP_OK);
    }
}