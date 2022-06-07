<?php

declare(strict_types=1);

namespace App\Controller\Bs;

use App\Controller\AbstractController;
use App\Service\Bs\OrderService;
use Hyperf\Di\Annotation\Inject;

class OrderController extends AbstractController
{
    /**
     * @Inject
     * @var OrderService
     */
    private $orderService;

    public function getAllOrder()
    {
        return $this->responseOk($this->orderService->getAllOrder());
    }
}
