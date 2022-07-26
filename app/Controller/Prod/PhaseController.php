<?php

namespace App\Controller\Prod;

use App\Controller\AbstractController;
use App\Service\Prod\PhaseService;
use Hyperf\Di\Annotation\Inject;

class PhaseController extends AbstractController
{
    /**
     * @Inject
     * @var PhaseService
     */
    private $phaseService;

    public function getPhaseByCode($code)
    {
        $code || ($code = $this->request->input('code'));

        if (is_null($code)) {
            $data = $this->phaseService->getPhases();
        } else {
            $data = $this->phaseService->getPhaseByCode($code);
        }
        return $this->responseOk($data);
    }

    public function saveCrudPhases(): array
    {
        $isOk = $this->phaseService->saveCrudPhases($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}