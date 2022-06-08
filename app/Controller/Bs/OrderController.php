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

        $queries = $this->request->query();
        $KhPONo = $queries['KhPONo'] ?? '';
        $spNo = $queries['spNo'] ?? '';
        $khNo = $queries['khNo'] ?? '';
        $company = $queries['company'] ?? '';

        $data = [];

        if (!$KhPONo && !$spNo && !$khNo) {
            return $this->responseOk($data);
        }

        $data = $this->orderService->getAllOrder($KhPONo, $spNo, $khNo, $company);
        return $this->responseOk($data);
    }

    public function getOrderDetails()
    {
        $queries = $this->request->query();
        $KhPONo = $queries['KhPONo'] ?? '';
        $spNo = $queries['spNo'] ?? '';
        $khNo = $queries['khNo'] ?? '';
        $OrdBIDs = $queries['OrdBIDs'] ?? '';
        $company = $queries['company'] ?? '';

        $data = [];

        if (!$KhPONo && !$spNo && !$khNo) {
            return $this->responseOk($data);
        }

        $data = $this->orderService->getOrderDetails($OrdBIDs, $KhPONo, $spNo, $khNo, $company);
        return $this->responseOk($data);
    }
}
