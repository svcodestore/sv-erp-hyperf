<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;
use App\Service\Hr\KPI\TitleService;
use Hyperf\Di\Annotation\Inject;

class TitleController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var TitleService
     */
    private $titleService;

    public function getAllTitle(): array
    {
        return $this->responseOk($this->titleService->getAllTitle());
    }
}