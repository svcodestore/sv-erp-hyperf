<?php

declare(strict_types=1);

namespace App\Controller\Prod;

use App\Controller\AbstractController;
use App\Service\Prod\ParamService;
use Hyperf\Di\Annotation\Inject;

class ParamController extends AbstractController
{
    /**
     * @Inject
     * @var ParamService
     */
    private $paramService;

    public function getAll(): array
    {
        $data = $this->paramService->getAll();

        return $this->responseOk($data);
    }

    public function getParamByKey(): array
    {
        $key = $this->request->query('key');
        $data = $this->paramService->getParamByKey($key);

        return $this->responseOk($data);
    }

    public function setParamByKey(): array
    {
        $key = $this->request->input('key');
        $update = $this->request->input('update');

        $data = $this->paramService->setParamByKey($key, $update);

        return $this->responseOk($data);
    }
}