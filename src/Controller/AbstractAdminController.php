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

namespace Mzh\Admin\Controller;

use Mzh\Admin\Traits\HasApiCreate;
use Mzh\Admin\Traits\HasApiDelete;
use Mzh\Admin\Traits\HasApiList;
use Mzh\Admin\Traits\HasApiPut;

abstract class AbstractAdminController
{
    use HasApiCreate,HasApiDelete,HasApiList,HasApiPut;
}
