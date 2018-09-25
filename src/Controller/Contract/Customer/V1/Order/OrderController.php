<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:27 PM
 */

namespace App\Controller\Contract\Customer\V1\Order;

use App\Infrastructure\DB\AlphaNumericGenerator;
use Doctrine\ODM\MongoDB\DocumentManager;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Service\Contract\Order\Model\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @param DocumentManager $documentManager
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(Request $request, DocumentManager $documentManager): JsonResponse
    {
        //TODO validate
        $generator = new AlphaNumericGenerator();
        $generator->setKey('orders');
        $orderId = $generator->generate($documentManager, new Order());

        // Get payload
        $payload = $request->request->all();



        $documentManager->persist(new Order());
        $documentManager->flush();
        return $this->json(['id' => 1], 200);
    }

    public function d()
    {
        
    }
}