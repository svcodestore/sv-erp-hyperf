<?php

declare(strict_types=1);

namespace App\Controller\Prod;

use App\Controller\AbstractController;
use App\Request\Prod\ScheduleRequest;
use App\Service\Prod\ScheduleService;
use App\Util\CurlUtil;
use Hyperf\Di\Annotation\Inject;
class ScheduleController extends AbstractController
{
    /**
     * @Inject
     * @var ScheduleService
     */
    private $scheduleService;

    public function schedule(ScheduleRequest $request)
    {
        $params = $request->validated();
        $data = $this->scheduleService->getScheduleList($params['workLine'], $params['year'], $params['month']);

        $p = ['prodLine' => $params['workLine'], 'date' => $params['year'] . '-' . ($params['month'] > 9 ? $params['month'] : '0' . $params['month'])];
        $res = json_decode(CurlUtil::post('http://192.168.123.51:11100/webApi/prod/autoSchedule', $p), true);

        // if (is_string($data)) {
        //     return $this->responseDetail($data);
        // }

        $rtnData = [];
        foreach ($res['data'] as $k => $item) {
            $rtnData[] = [
                'workshop' => $params['workLine'],
                'workshop_name' => $item['ppi_workshop_name'],
                'customer_no' => $item['ppi_customer_no'],
                'customer_po_no' => $item['ppi_customer_pono'],
                'item_code' => $item['ppi_prd_item'],
                'item_qty' => $item['ppi_expected_qty'],
                'po_month' => $params['month'],
                'po_year' => $params['year'],
                'phases' => [],
            ];
            foreach ($item['phases'] as $value) {
                $rtnData[$k]['phases'][] = [
                    'code' => $item['ppi_prd_item'],
                    'code_id' => $value['map_ppi_phsid'],
                    'name' => $value['map_ppi_phs'],
                    'cost_time' => $value['map_ppi_cost_time'],
                    'is_master' => (int)$value['map_ppi_isvice'] === 0 ? 1 : 0,
                    'ahead_time' => $value['map_ppi_aheadtime'],
                    'dead_time' => $value['map_ppi_deadtime'],
                    'out_time' => $value['map_ppi_outime'],
                    'worker_num' => $value['map_ppi_worker_num'],
                    'start_at' => $value['ppi_phs_start'],
                    'complete_at' => $value['ppi_phs_complete'],
                ];
            }
        }

        return $this->responseOk($rtnData);
    }

    public function getPhaseByCode($code)
    {

        $code || ($code = $this->request->input('code'));

        $data = [];
        if (is_null($code)) {
            $data = $this->scheduleService->getPhases();
        } else {
            $data = $this->scheduleService->getPhaseByCode($code);
        }
        return $this->responseOk($data);
    }

    public function getPo(ScheduleRequest $request)
    {
        $params = $request->validated();
        $data = $this->scheduleService->getMonthPo($params['workLine'], $params['year'], $params['month']);
        return $this->responseOk($data);
    }
}
