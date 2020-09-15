<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HPlus\Admin\Controller;

use HPlus\Admin\Traits\HasApiCreate;
use HPlus\Admin\Traits\HasApiDelete;
use HPlus\Admin\Traits\HasApiList;
use HPlus\Admin\Traits\HasApiPut;

abstract class AbstractAdminController
{
    use HasApiCreate,HasApiDelete,HasApiList,HasApiPut;
}
