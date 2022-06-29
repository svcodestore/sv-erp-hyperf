<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Service\Hr\KPI\RuleService;
use Hyperf\Di\Annotation\Inject;

class RuleController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var RuleService
     */
    private $ruleService;

    public function getAll(): array
    {
        return $this->responseOk($this->ruleService->getAll());
    }

    public function saveCrud()
    {
        $isOk = $this->ruleService->saveCrud($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
