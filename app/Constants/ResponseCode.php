<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ResponseCode extends AbstractConstants
{
    /**
     * @Message("Server Error! ")
     */
    public const SERVER_ERROR = 500;

    /**
     * @Message("Response Ok! ")
     */
    public const RESPONSE_OK = 0;
}
