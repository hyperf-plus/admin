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
use HPlus\Admin\Admin;
use HPlus\Admin\Model\Admin\Administrator;
use HPlus\Admin\Model\Admin\Menu;
use HPlus\Admin\Model\Admin\RoleMenu;

/**
 * 角色管理
 * @AdminController(prefix="roles"))
 * Class Auth
 * @package App\Controller
 */
class Roles extends AbstractAdminController
{

    protected function grid()
    {
        $roleModel = config('admin.database.roles_model');
        $roles_table = config('admin.database.role_menu_table');
        $grid = new Grid(new $roleModel());
        $grid->quickSearch(['slug', 'name']);
        $grid->column('id', 'ID')->width('80px')->sortable();
        $grid->column('slug', "标识");
        $grid->column('name', "名称");
        $grid->column('menu.title', "权限")->component(Tag::make()->type('info'));
        $grid->column('created_at');
        $grid->column('updated_at');
        $grid->ref('roleEdit');
        $userData = Administrator::get()->map(function ($item) {
            return TransferData::make($item->id, $item->name);
        });

        $tree = (new Tree)->dataUrl(route('/admin/roles/tree', []));
        $tree->column('title', '标题');
        $tree->style('margin-top: -10px;');
        $tree->setGetData(false);
        $tree->ref('roleTree')->showHeader(false);
        $grid->actions(function (Grid\Actions $action) use ($userData, $tree) {
            $auth = new Grid\Actions\ActionButton('授权用户');
            $auth->dialog(function (Dialog $dialog) use ($action, $userData) {
                $dialog->title('授权用户');
                $dialog->width('700px');
                $dialog->slot(function (Content $content) use ($action, $userData) {
                    $roleModel = config('admin.database.roles_model');
                    $row = $action->getRow();
                    $form = new Form(new $roleModel());
                    $form->isDialog()->isGetData(false);
                    $form->item('permissions', "权限", 'permissions.id')->component(
                        Transfer::make()->data($userData)->titles(['可授权', '已授权'])->filterable()
                    )->defaultValue($row->permissions);
                    $content->body($form);
                });
            });
            $action->add($auth);
            $ids = RoleMenu::query()->where('role_id', $action->getRow()->id)->get(['menu_id'])->pluck('menu_id')->toArray();
            unset($auth);
            $auth = new Grid\Actions\ActionButton('设置');
            $auth->dialog(function (Dialog $dialog) use ($action, $userData, $tree, $ids) {
                $dialog->title('权限配置');
                $dialog->width('700px');
                $dialog->ref('roleTree');
                $dialog->slot(function (Content $content) use ($action, $userData, $tree, $ids) {
                    $tree->setCheckedKeys([]);
                    $content->row($tree->setCheckedKeys($ids));
                    $content->row($this->br());
                    $js = <<<JS
console.log(ref.\$store.getters)
const keys = ref.\$store.getters.thisPage.grids.treeSelectionKeys;
console.log(keys)
JS;
                    $content->row(Button::make('确定')->refData('roleEdit', $js));
                });
            });
            $action->add($auth);
        });
        return $grid;
    }

    /**
     * 重写list 默认也可以不写此方法
     * @GetMapping(path="tree")
     * @return array|mixed
     */
    public function tree()
    {
        $menuModel = config('admin.database.menu_model');
        /** @var Menu $model */
        $model = new $menuModel;
        return Admin::response(['data' => $model->toTree()]);
    }

    private function menuGrid()
    {
        $grid = new Grid(new Menu());
        $grid->emptyText('暂无数据');
        $grid->dataUrl(route('/demo/list'));
        $grid->selection();
        $grid->setGetData(false);
        $grid->column('id', 'ID')->width('80px')->sortable();
        $grid->column('uri', "标识");
        $grid->column('title', "名称");
        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->hide();
        });
        $grid->hideActions();
        $grid->hidePage();
        return $grid;
    }

    protected function form()
    {
        $adminMenuModel = config('admin.database.menu_model');
        $roleModel = config('admin.database.roles_model');
        $form = new Form(new $roleModel());

        $adminMenu = new $adminMenuModel;
        $form->item('slug', "标识")->required();
        $form->item('name', "名称")->required();
        $data = $adminMenu::selectOptions(function ($permission) {
            return $permission::query()->where('parent_id', 0)->orderBy('order');
        },'请选择')->map(function ($name,$id) {
            return TransferData::make($id, $name);
        });
        $form->item('menu', "权限", 'menu.menu_id')->component(
            Transfer::make()->data(array_values($data->toArray()))->titles(['可授权', '已授权'])->filterable()
        );
        return $form;
    }
}
