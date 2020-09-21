<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
namespace HPlus\Admin\Controller;

use HPlus\Admin\Service\AuthService;
use HPlus\Route\Annotation\AdminController;
use HPlus\Route\Annotation\GetApi;
use HPlus\UI\Components\Attrs\SelectOption;
use HPlus\UI\Components\Form\CSwitch;
use HPlus\UI\Components\Form\IconChoose;
use HPlus\UI\Components\Form\InputNumber;
use HPlus\UI\Components\Form\Select;
use HPlus\UI\Components\Grid\Icon;
use HPlus\UI\Components\Grid\Tag;
use HPlus\UI\Form;
use HPlus\UI\Grid;
use Hyperf\Database\Model\Model;
use Hyperf\Utils\Str;

/**
 * @AdminController(tag="菜单管理"))
 */
class Menu extends AbstractAdminController
{
    /**
     * @GetApi(path="route", summary="路由列表")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function route()
    {
        $kw = $this->request->query('query', '');
        $routes = make(AuthService::class)->getSystemRouteOptions(true);
        $routes = array_filter($routes, function ($item) use ($kw) {
            if (empty($kw)) {
                return true;
            }
            return Str::contains($item['value'], $kw);
        });
        $routes = array_values($routes);
        return $this->response->json(['code' => 200, 'data' => ['data' => $routes, 'total' => count($routes)]]);
    }

    protected function grid()
    {
        $menuModel = config('admin.database.menu_model');
        $grid = new Grid(new $menuModel());
        $parent_id = $this->request->query('parent_id', 0);
        $grid->model()->where('parent_id', $parent_id);
        $grid->model()->with(['children', 'roles', 'children.roles']);
        $grid
            ->defaultSort('order', 'asc')
            ->tree()
            ->emptyText('暂无菜单')
            ->quickSearch(['title'])
            ->dialogForm($this->form()->isDialog()->backButtonName('关闭'));
        $grid->column('id', 'ID')->width(80);
        $grid->column('icon', '图标')->component(Icon::make())->width(80);
        $grid->column('title', '名称');
        $grid->column('order', '排序');
        $grid->column('uri', '路径');
        $grid->column('roles.name', '授权角色')->component(Tag::make());
        $grid->hidePage();
        return $grid;
    }

    protected function form()
    {
        /*@var Model $model */
        $model = config('admin.database.menu_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');
        $form = new Form(new $model());
        $form->size('medium');
        $form->className('m-10');
        $form->item('parent_id', '上级目录')->component(Select::make(0)->options(function () use ($model) {
            /* @var \HPlus\Admin\Model\Admin\Menu $model */
            return $model::selectOptions(function ($model) {
                $model::query()->where('parent_id', 0)->orderBy('order');
            }, '根目录')->map(function ($title, $id) {
                return SelectOption::make($id, $title);
            });
        }));
        $form->item('title', '名称')->required();
        $form->item('icon', '图标')->component(IconChoose::make())->ignoreEmpty();
        $form->item('uri', 'URI')->required()
            ->help('可以输入搜索')
            ->component(Select::make()->filterable()
            ->remote(route('menu/route')))->inputWidth(450);
        $form->item('order', '排序')->component(InputNumber::make(1)->min(0));
        $form->item('is_menu', '设为菜单')->component(CSwitch::make(0));
        $form->item('roles', '角色')->component(Select::make()->block()->multiple()->options(function () use ($roleModel) {
            return $roleModel::all()->map(function ($role) {
                return SelectOption::make($role->id, $role->name);
            });
        }));
        //编辑前置钩子
        $form->editing(function (Form $form) {
        });
        //编辑中钩子
        $form->saving(function (Form $form) {
        });
        //编辑后置钩子
        $form->saved(function (Form $form) {
        });
        if ((new $model())->withPermission()) {
            $form->item('permission', '权限')->component(Select::make()->clearable()->block()->multiple()->options(function () use ($permissionModel) {
                return $permissionModel::all()->map(function ($role) {
                    return SelectOption::make($role->id, $role->name);
                });
            }));
        }
        return $form;
    }
}
