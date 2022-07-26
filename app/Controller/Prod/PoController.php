<?php

namespace App\Controller\Prod;

use App\Controller\AbstractController;
use App\Request\Prod\ScheduleRequest;
use App\Service\Prod\PoService;
use Hyperf\Di\Annotation\Inject;

class PoController extends AbstractController
{
    /**
     * @Inject
     * @var PoService
     */
    private $poService;

    public function getPo(ScheduleRequest $request): array
    {
        $params = $request->validated();
        $data = $this->poService->getMonthPo($params['workLine'], $params['year'], $params['month']);

        return $this->responseOk($data);
    }

    public function deletePoById(int $id): array
    {
        $isOk = $this->poService->deletePoWithId($id);

        if ($isOk) {
            return $this->responseOk(true);
        }
        return $this->responseDetail();
    }

    public function addPo(): array
    {
        $add = $this->request->input('add');
        $isOk = $this->poService->addPo($add);

        if ($isOk) {
            return $this->responseOk(true);
        }
        return $this->responseDetail();
    }

    public function updatePoById(int $id): array
    {
        $update = $this->request->input('update');
        $isOk = $this->poService->updatePoWithId($id, $update);

        if ($isOk) {
            return $this->responseOk(true);
        }
        return $this->responseDetail();
    }
}