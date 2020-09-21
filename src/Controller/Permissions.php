<?php
declare(strict_types=1);

namespace HPlus\Admin\Controller;

use HPlus\Route\Annotation\AdminController;
use HPlus\UI\Components\Attrs\SelectOption;
use HPlus\UI\Components\Form\Select;
use HPlus\UI\Components\Grid\Tag;
use HPlus\UI\Form;
use HPlus\UI\Grid;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\Utils\Str;
use HPlus\Admin\Service\AuthService;

/**
 * @AdminController(prefix="permissions",tag="权限管理"))
 * @package App\Controller
 */
class Permissions extends AbstractAdminController
{
    /**
     * 重写标题
     * @return string
     */
    protected function title()
    {
        return '系统权限管理';
    }

    protected function grid()
    {
        $permissionModel = config('admin.database.permissions_model');
        $grid = new Grid(new $permissionModel());
        //$grid->dataUrl(route('admin.permissions.route'));
        $grid->tree();

        $grid->model()->where('parent_id', 0);
        $grid->model()->with(['children', 'roles', 'children.roles']);

        $grid->quickSearch(['kw', '搜索关键词']);
        $grid->tree();
        $grid->column('name', "名称");
        $grid->column('slug', "标识");
        $grid->column('path', "授权节点")->component(Tag::make());
        $grid->dialogForm($this->form()->isDialog()->className('p-15')->labelWidth('auto'), '600px', ['添加权限', '编辑权限']);
        return $grid;
    }


    protected function form($isEdit = false)
    {
        $permissionModel = config('admin.database.permissions_model');

        $form = new Form(new $permissionModel());
        $form->item('parent_id', '上级目录')->component(Select::make(0)->options(function () use ($permissionModel) {
            return $permissionModel::selectOptions(function ($permission) {
                return $permission::query()->where('parent_id', 0)->orderBy('order');
            }, '无分组')->map(function ($title, $id) {
                return SelectOption::make($id, $title);
            });
        }));
        $form->item('name', "名称")->required();
        $form->item('slug', "标识")->required();
        $form->item('path', "授权节点")
            ->help('可以输入搜索')
            ->component(Select::make()->filterable()
                ->remote(route('permissions/route'))->multiple())->inputWidth(450);
        return $form;
    }

    protected function getHttpMethodsOptions()
    {
        $model = config('admin.database.permissions_model');
        return collect($model::$httpMethods)->map(function ($item) {
            return SelectOption::make($item, $item);
        })->toArray();
    }

    /**
     * @GetMapping(path="route")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function route()
    {
        $kw = $this->request->query('query', '');
        $routes = make(AuthService::class)->getSystemRouteOptions();
        $routes = array_filter($routes, function ($item) use ($kw) {
            if (empty($kw)) return true;
            return Str::contains($item['value'], $kw);
        });
        $routes = array_values($routes);
        return $this->response->json(['code' => 200, 'data' => ['data' => $routes, 'total' => count($routes)]]);
    }
}
