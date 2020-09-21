<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf/hyperf-plus/blob/master/LICENSE
 */
namespace HPlus\Admin\Controller;

use HPlus\Admin\Traits\HasApiCreate;
use HPlus\Admin\Traits\HasApiDelete;
use HPlus\Admin\Traits\HasApiList;
use HPlus\Admin\Traits\HasApiPut;

abstract class AbstractAdminController
{
    use HasApiCreate;
    use HasApiDelete;
    use HasApiList;
    use HasApiPut;
}
