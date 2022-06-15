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
    $limit = $request->input('limit', 1000);
    $search = [
      'left_time' => $request->input('left_time'),
      'right_time' => $request->input('right_time'),
      'mache_num' => $request->input('mache_num'),
      'reporter_id' => $request->input('reporter_id'),
      'noreach' => $request->input('noreach'),
      'cause' => $request->input('cause'),
    ];
    return $service->getRecord($search, $limit);
  }
}
