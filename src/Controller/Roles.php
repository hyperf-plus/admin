<?php
declare(strict_types=1);

namespace HPlus\Admin\Controller;

use HPlus\Route\Annotation\AdminController;
use HPlus\UI\Components\Attrs\TransferData;
use HPlus\UI\Components\Form\Transfer;
use HPlus\UI\Components\Grid\Tag;
use HPlus\UI\Components\Widgets\Button;
use HPlus\UI\Components\Widgets\Dialog;
use HPlus\UI\Form;
use HPlus\UI\Grid;
use HPlus\UI\Layout\Content;
use HPlus\UI\Tree;
use Hyperf\HttpServer\Annotation\GetMapping;
/**
 * 角色管理
 * @AdminController(prefix="roles"))
 * @package App\Controller
 */
class Roles extends AbstractAdminController
{



    protected function grid()
    {
        $roleModel = config('admin.database.roles_model');

        $grid = new Grid(new $roleModel());

        $grid->quickSearch(['slug', 'name']);

        $grid->column('id', 'ID')->width('80px')->sortable();
        $grid->column('slug', "标识");
        $grid->column('name', "名称");
        $grid->column('permissions.name', "权限")->component(Tag::make()->type('info'));
        $grid->column('created_at');
        $grid->column('updated_at');
        $grid->dialogForm($this->form()->isDialog()->labelWidth("auto")->className('p-15'), '700px', ['添加角色', '编辑角色']);
        return $grid;
    }

    public function form()
    {
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');
        $form = new Form(new $roleModel());

        $form->item('slug', "标识")->required()->inputWidth(8);
        $form->item('name', "名称")->required()->inputWidth(8);
        $form->item('permissions', "权限", 'permissions.id')->component(
            Transfer::make()->data($permissionModel::get()->map(function ($item) {
                return TransferData::make($item->id, $item->name);
            }))->titles(['可授权', '已授权'])->filterable()
        );
        return $form;
    }
}
