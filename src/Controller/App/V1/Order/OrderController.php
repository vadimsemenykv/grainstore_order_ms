<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 7:27 PM
 */

namespace App\Controller\App\V1\Order;

use Service\Order\Model\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController extends Controller
{
    public function create(): JsonResponse
    {
        return $this->json(['id' => 1], 200);
    }

    public function d()
    {
        
    }
}