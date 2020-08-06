<?php
declare(strict_types=1);

namespace Mzh\Admin\Controller\Api;

use Mzh\Admin\Controller\AbstractController;
use Mzh\Admin\Model\AuthRule as AdminAuthRule;
use Mzh\Admin\Traits\GetApiBatchDel;
use Mzh\Admin\Traits\GetApiDelete;
use Mzh\Admin\Traits\GetApiDetail;
use Mzh\Admin\Traits\GetApiList;
use Mzh\Admin\Traits\GetApiRowChange;
use Mzh\Admin\Traits\GetApiUI;
use Mzh\Admin\Traits\GetApiUpdate;
use Mzh\Admin\Validate\AuthRuleValidation;
use Mzh\Admin\Views\AuthRuleView;
use Mzh\Swagger\Annotation\ApiController;

/**
 * @ApiController(tag="AuthRule模块")
 */
class AuthRule extends AbstractController
{
    public $validateClass = AuthRuleValidation::class;
    public $modelClass = AdminAuthRule::class;
    public $viewClass = AuthRuleView::class;

    use GetApiUI;
    use GetApiList;
    use GetApiDelete;
    use GetApiDetail;
    use GetApiUpdate;
    use GetApiBatchDel;
    use GetApiRowChange;

    protected function _form_before(&$data)
    {
        if ($this->isGet()) {
            return;
        }

    }


}
