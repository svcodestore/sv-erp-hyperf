<?php

/**
 * MachineController
 * TPM 机器设备相关 API Controller
 */

namespace App\Controller\TPM;

use App\Model\TPM\MachineModel;
use App\Service\TPM\MachineService;
use App\Service\TPM\MachineTemplateService;
use Hyperf\HttpServer\Contract\RequestInterface;

class MachineController extends \App\Controller\AbstractController
{

  /**
   * 机器列表
   */
  public function machines(RequestInterface $request, MachineService $service)
  {
    $limit = $request->input('limit', 10000);
    $page = $request->input('page', 0);
    $line_num = $request->input('line_num');
    $status = $request->input('status');

    return $service->getMachines($line_num, $status, $page, $limit);
  }


  public function machine_detaile()
  {
  }

  /**
   * 机器配件类型模板
   * 
   */
  public function machine_templates(MachineTemplateService $service)
  {
    return $service->getMachineTemplates();
  }

  /**
   * 对应机器类型(模板)的配件
   */
  public function machine_fittings(RequestInterface $request, MachineTemplateService $service)
  {
    $template_id = $request->input('template_id');

    return $service->getTemplateFittings($template_id);
  }
}
