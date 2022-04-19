<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class RedisKey extends AbstractConstants
{
	public const ISSUED_ACCESS_TOKEN = "issuedAccessToken";
}
