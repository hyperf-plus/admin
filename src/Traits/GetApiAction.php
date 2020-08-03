<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

trait GetApiAction
{
    use GetApiList;
    use GetApiCreate;
    use GetApiUpdate;
    use GetApiDelete;
    use GetApiState;
    use GetApiSort;
    use GetApiDetail;
    use GetApiBatchDel;
    use GetApiUI;
    use GetApiRowChange;
}