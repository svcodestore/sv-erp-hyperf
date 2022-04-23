<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Model;

interface IService
{
    public function all(Model $m,array $cond): array;
}
