<?php
declare(strict_types=1);
namespace HPlus\Admin\Controller;

use HPlus\Admin\Library\Auth;
use HPlus\Route\Annotation\AdminController;
use HPlus\UI\Components\Attrs\SelectOption;
use HPlus\UI\Components\Form\Input;
use HPlus\UI\Components\Form\Select;
use HPlus\UI\Components\Form\Upload;
use HPlus\UI\Components\Grid\Avatar;
use HPlus\UI\Components\Grid\Tag;
use HPlus\UI\Form;
use HPlus\UI\Layout\Row;
use HPlus\UI\Grid;
use HPlus\UI\UI;
use Hyperf\HttpServer\Annotation\Controller;
use HPlus\Admin\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middleware;

/**
 * @Middleware(PermissionMiddleware::class)
 * @AdminController(prefix="users",tag="管理员管理",ignore=true))
 * @package HPlus\Admin\Controllers
 */
class Users extends AbstractAdminController
{
    protected function grid()
    {
        $userModel = config('admin.database.users_model');
        $grid = new Grid(new $userModel());
        $grid
            ->quickSearch(['name', 'username'])
            ->quickSearchPlaceholder("用户名 / 名称")
            ->pageBackground()
            ->defaultSort('id', 'asc')
            ->selection()
            ->stripe(true)->emptyText("暂无用户")
            ->perPage(10)
            ->autoHeight();

        $grid->column('id', "ID")->width(80);
        $grid->column('avatar', '头像')->width(80)->align('center')->component(Avatar::make());
        $grid->column('username', "用户名");
        $grid->column('name', '用户昵称');
        $grid->column('roles.name', "角色")->component(Tag::make()->effect('dark'));
        $grid->column('created_at');
        $grid->column('updated_at');
        return $grid;
    }

    protected function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');
        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');

        $form->item('avatar', '头像')->component(Upload::make()->avatar()->path('avatar')->uniqueName());
        $form->row(function (Row $row, Form $form) use ($userTable) {
            $row->column(8, $form->rowItem('username', '用户名')
                ->serveCreationRules(['required', "unique:{$userTable}"])
                ->serveUpdateRules(['required', "unique:{$userTable},username,{{id}}"])
                ->component(Input::make())->required());
            $row->column(8, $form->rowItem('name', '名称')->component(Input::make()->showWordLimit()->maxlength(20))->required());
        });

        $form->row(function (Row $row, Form $form) {
            $row->column(8, $form->rowItem('password', '密码')->serveCreationRules(['required', 'string', 'confirmed'])->serveUpdateRules(['confirmed'])->ignoreEmpty()
                ->component(function () {
                    return Input::make()->password()->showPassword();
                }));

            $row->column(8, $form->rowItem('password_confirmation', '确认密码')
                ->copyValue('password')->ignoreEmpty()
                ->component(function () {
                    return Input::make()->password()->showPassword();
                }));
        });
        $form->item('roles', '角色')->component(Select::make()->block()->multiple()->options($roleModel::all()->map(function ($role) {
            return SelectOption::make($role->id, $role->name);
        })->toArray()));
        $form->item('permissions', '权限')->component(Select::make()->clearable()->block()->multiple()->options($permissionModel::all()->map(function ($role) {
            return SelectOption::make($role->id, $role->name);
        })->toArray()));

        $form->saving(function (Form $form) {
            if ($form->password) {
                $form->password = password_hash($form->password,PASSWORD_DEFAULT);
            }
        });
        $form->deleting(function (Form $form, $id) {
            if (Auth()->user()->getId() == $id || $id == 1) {
               return UI::responseError("删除失败");
           }
        });
        return $form;
    }
}
