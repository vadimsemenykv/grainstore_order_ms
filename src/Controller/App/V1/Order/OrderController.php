<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:27 PM
 */

namespace App\Controller\App\V1\Order;

use Contract\Service\Order\Model\Order;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController extends Controller
{
    public function create(DocumentManager $documentManager): JsonResponse
    {

        $documentManager->persist(new Order());
        $documentManager->flush();
        return $this->json(['id' => 1], 200);
    }

    public function d()
    {
        
    }
}