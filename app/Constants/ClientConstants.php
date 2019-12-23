<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ClientConstants extends AbstractConstants
{

    /**
     * @Message("顾客")
     */
    const TYPE_USER = 0;

    /**
     * @Message("管理组")
     */
    const TYPE_ADMIN = 1;

    /**
     * @Message("游客")
     */
    const TYPE_GUEST = -1;
}
