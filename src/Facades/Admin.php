<?php
declare(strict_types=1);

namespace HPlus\Admin\Facades;


use HPlus\UI\Form;
use HPlus\UI\Grid;
use HPlus\UI\Layout\Content;
use HPlus\UI\Tree;
use Hyperf\HttpMessage\Server\Response;

/**
 * Class Admin.
 *
 * @method static Grid grid($model, \Closure $callable)
 * @method static Form form($model, \Closure $callable)
 * @method static Tree tree($model, \Closure $callable = null)
 * @method static Content content(\Closure $callable = null)
 * @method static string title()
 * @method static Response response($data, $message = '', $code = 200, $headers = [])
 * @method static Response responseMessage($message = '', $code = 200)
 * @method static Response responseError($message = '', $code = 400)
 * @method static Response responseRedirect($url, $isVueRoute = true, $message = null, $type = 'success')
 * @method static array menu()
 * @method static void route()
 *
 * @see \HPlus\Admin\Admin
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
        return \HPlus\Admin\Admin::class;
    }
}
