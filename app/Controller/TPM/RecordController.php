<?php

/**
 * RecordController
 * TPM 维修记录 Controller
 * 
 */

namespace App\Controller\TPM;

use App\Model\TPM\RecordModel;
use App\Service\TPM\RecordService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class RecordController extends AbstractController
{

  /**
   * 获取维修记录
   */
  public function records(RequestInterface $request, ResponseInterface $response)
  {
    $service = new RecordService();
    return $service->getRecord($request, $response);
  }
}
