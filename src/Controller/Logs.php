<?php
declare(strict_types=1);

namespace HPlus\Admin\Controller;

use HPlus\Route\Annotation\AdminController;
use HPlus\UI\Components\Attrs\SelectOption;
use HPlus\UI\Components\Form\Input;
use HPlus\UI\Components\Form\Select;
use HPlus\UI\Components\Grid\Avatar;
use HPlus\UI\Components\Grid\Route;
use HPlus\UI\Components\Grid\Tag;
use HPlus\UI\Form;
use HPlus\UI\Grid;
use Hyperf\Database\Model\Model;
use HPlus\Admin\Model\Admin\OperationLog;

/**
 * @AdminController(prefix="logs",tag="日志管理"))
 * @package HPlus\Admin\Controllers
 */
class Logs extends AbstractAdminController
{
    protected function grid()
    {
        $grid = new Grid(new OperationLog());
        $grid->perPage(20)
            ->selection()
            ->defaultSort('id', 'desc')
            ->stripe()
            ->emptyText("暂无日志")
            ->height('auto')
            ->appendFields(["user.id"]);
        $grid->column('id', "ID")->width("100");
        $grid->column('user.avatar', '头像', 'user_id')->component(Avatar::make()->size('small'))->width(80);
        $grid->column('user.name', '用户', 'user_id')->help("操作用户")->sortable()->component(Route::make("/admin/logs/list?user_id={user.id}")->type("primary"));
        $grid->column('method', '请求方式')->width(100)->align('center')->component(Tag::make()->type(['GET' => 'info', 'POST' => 'success']));
        $grid->column('path', '路径')->help('操作URL')->sortable();
        $grid->column('runtime', '执行时间')->help('毫秒');
        $grid->column('ip', 'IP')->component(Route::make("/admin/logs/list?ip={ip}")->type("primary"));
        $grid->column('created_at', "创建时间")->sortable();

        $grid->actions(function (Grid\Actions $actions) {
            $actions->hideEditAction();
            $actions->hideViewAction();
        })->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->hideCreateButton();
        });

        $grid->filter(function (Grid\Filter $filter) {
            $user_id = (int)request('user_id');
            $filter->equal('user_id')->component(Select::make($user_id)->placeholder("请选择用户")->options(function () {
                $user_ids = OperationLog::query()->groupBy("user_id")->get(["user_id"])->pluck("user_id")->toArray();
                /**@var Model $userModel */
                $userModel = config('admin.database.users_model');
                return $userModel::query()->whereIn("id", $user_ids)->get()->map(function ($user) {
                    return SelectOption::make($user->id, $user->name);
                })->all();
            }));
            $filter->equal('ip')->component(Input::make(request('ip'))->placeholder("IP"));
        });

        return $grid;
    }

    protected function form($isEdit = false)
    {
        $form = new Form(new OperationLog());
        $form->setEdit($isEdit);
        return $form;
    }
}
