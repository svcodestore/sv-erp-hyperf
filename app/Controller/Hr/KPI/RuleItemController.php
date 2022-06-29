<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Service\Hr\KPI\RuleItemService;
use Hyperf\Di\Annotation\Inject;

class RuleItemController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var RuleItemService
     */
    private $ruleItemService;

    public function getAll(): array
    {
        return $this->responseOk($this->ruleItemService->getAll());
    }

    public function saveCrud()
    {
        $isOk = $this->ruleItemService->saveCrud($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
