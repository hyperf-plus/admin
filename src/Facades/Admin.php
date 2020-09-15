<?php
declare(strict_types=1);

namespace Mzh\Admin\Facades;


use HPlus\UI\Form;
use HPlus\UI\Grid;
use HPlus\UI\Layout\Content;
use HPlus\UI\Tree;

/**
 * Class Admin.
 *
 * @method static Grid grid($model, \Closure $callable)
 * @method static Form form($model, \Closure $callable)
 * @method static Tree tree($model, \Closure $callable = null)
 * @method static Content content(\Closure $callable = null)
 * @method static string title()
 * @method static array menu()
 * @method static void route()
 *
 * @see \Mzh\Admin\Admin
 */
class Admin extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Mzh\Admin\Admin::class;
    }
}
