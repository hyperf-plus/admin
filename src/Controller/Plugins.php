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

use HPlus\Admin\Annotation\AdminPlugins;
use HPlus\Admin\Contract\AbstractAdminPlugin;
use HPlus\Admin\Exception\BusinessException;
use HPlus\Admin\Library\Auth;
use HPlus\Admin\Model\Plugin;
use HPlus\Route\Annotation\AdminController;
use HPlus\Route\Annotation\Query;
use HPlus\UI\Components\Attrs\SelectOption;
use HPlus\UI\Components\Attrs\Step;
use HPlus\UI\Components\Form\CSwitch;
use HPlus\UI\Components\Form\Input;
use HPlus\UI\Components\Form\Select;
use HPlus\UI\Components\Form\Upload;
use HPlus\UI\Components\Grid\Avatar;
use HPlus\UI\Components\Grid\Tag;
use HPlus\UI\Components\Widgets\Dialog;
use HPlus\UI\Components\Widgets\Html;
use HPlus\UI\Components\Widgets\Steps;
use HPlus\UI\Form;
use HPlus\UI\Form\FormActions;
use HPlus\UI\Grid;
use HPlus\UI\Layout\Content;
use HPlus\UI\Layout\Row;
use HPlus\UI\UI;
use HPlus\Route\Annotation\GetApi;
use HPlus\Route\Annotation\PostApi;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\ReflectionManager;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Utils\Collection;

/**
 * @AdminController(prefix="plugin", tag="插件管理", ignore=true))
 */
class Plugins extends AbstractAdminController
{
    /**
     * @GetApi(summary="获取列表")
     * @return array|mixed
     */
    public function list()
    {
        if ($this->isGetData()) {
            return $this->grid()->customData($this->data());
        }
        $content = new Content();
        //可以重写这里，实现自定义布局
        $content->body($this->grid())->title('插件列表')->className('p-15');
        return $content->jsonSerialize();
    }

    /**
     * @RequestMapping(summary="获取列表")
     * @return array|mixed
     */
    public function setting()
    {
        $name = $this->request->query('id');
        $pluginModel = Plugin::find($name);
        if (!$pluginModel) {
            throw new BusinessException(403, "插件未安装");
        }
        $plugin = $this->getPlugin($pluginModel->name);
        if ($this->isPost()) {
            $pluginModel->config = $this->request->getParsedBody();
            $pluginModel->save();
            return UI::responseMessage('设置成功');
        }
        /**
         * @var Form $form
         */
        $form = $plugin->configForm();
        if (!$form instanceof Form) {
            return $form;
        }
        $form->action(route('plugin/setting', ['id' => $name]));
        $form->setFormValue($pluginModel->config);
        $content = new Content();
        $content->body($form)->className('m-10');
        $content->showHeader(true)->title('插件设置');
        return $content;
    }

    /**
     * @PostApi(summary="安装插件")
     * @Query(key="id")
     * @return array|mixed
     */
    public function install()
    {
        $config = $this->request->getParsedBody();
        $id = $this->request->query('id');
        $pluginClass = $this->getPlugin($id);
        $plugin = Plugin::find($id);
        if ($plugin) {
            throw new BusinessException(403, "该插件已安装");
        }
        if (!$pluginClass->install()) {
            throw new BusinessException(403, "安装失败");
        }
        $pluginInfo = AnnotationCollector::getClassAnnotation(get_class($pluginClass), AdminPlugins::class);
        $Plugin = new Plugin();
        $Plugin->name = $pluginInfo->name;
        $Plugin->description = $pluginInfo->description;
        $Plugin->title = $pluginInfo->title;
        $Plugin->version = $pluginInfo->version;
        $Plugin->author = $pluginInfo->author;
        $Plugin->demo_url = $pluginInfo->demo_url;
        $Plugin->author_url = $pluginInfo->author_url;
        $Plugin->status = $config['plugin_status'] ?? 0;
        unset($config['plugin_status']);
        $Plugin->config = $config;
        $Plugin->save();
        return UI::responseMessage("安装成功");
    }

    /**
     * @PostApi(summary="卸载插件")
     * @Query(key="id")
     * @return array|mixed
     */
    public function uninstall()
    {
        $id = $this->request->query('id');
        $plugin = Plugin::find($id);
        if (!$plugin) {
            throw new BusinessException(403, "该插件未安装");
        }
        $pluginClass = $this->getPlugin($plugin->name);
        if (!$pluginClass->uninstall()) {
            throw new BusinessException(403, "卸载失败,插件文件不存在");
        }
        $plugin->delete();
        return UI::responseMessage("卸载成功");
    }

    public function data()
    {
        $listPlugin = AnnotationCollector::getClassesByAnnotation(AdminPlugins::class);
        $listPlugin = collect($listPlugin)->keyBy('name');
        $list = Plugin::get()->keyBy('name');
        $list = $listPlugin->merge($list);
        return $list->toArray();
    }

    protected function grid()
    {
        $grid = new Grid();
        $grid
            ->stripe(true)->emptyText('暂无插件')
            ->perPage(10)
            ->hidePage();
        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->hide();
        });
        $grid->rowKey('name');
        $grid->column('name', '插件标识');
        $grid->column('title', '插件名称')->width(200);
        $grid->column('description', '描述');
        $grid->column('author', '作者');
        $grid->column('version', '版本');
        $grid->column('status', '状态')->customValue(function ($item) {
            $status = $item['status'] ?? 0;
            return $status == 1 ? '已安装' : '未安装';
        });
        $grid->actions(function (Grid\Actions $actions) {
            $actions->hideDeleteAction();
            $actions->hideEditAction();
            $item = $actions->getRow();
            $id = $item['name'] ?? '';
            try {
                $plugin = $this->getPlugin($id);
            } catch (BusinessException $exception) {
                $action = new Grid\Actions\ActionButton('无效插件');
                $action->disabled();
                $actions->add($action);
                return;
            }
            if (!isset($item['id'])) {
                $action = new Grid\Actions\ActionButton('安装');
                $action->dialog(function (Dialog $dialog) use ($plugin, $id) {
                    $dialog->title("安装插件");
                    $dialog->slot(function (Content $content) use ($plugin, $id) {
                        /**
                         * @var Form $form
                         */
                        $form = $plugin->configForm();
                        $form->item('name', '插件标识')->defaultValue($id)->component(Input::make()->disabled());
                        $form->actions(function (FormActions $actions) {
                            $actions->hideCancelButton();
                            $actions->submitButton()->content('安装');
                        });
                        $form->action(route('plugin/install', ['id' => $id]));
                        $form->item('plugin_status', '插件状态')->component(CSwitch::make(1));
                        $form->successRefData("tableReload");
                        $content->body($form);
                    });
                });
                $actions->add($action);
            } else {
                $action = new Grid\Actions\ActionButton('设置');
                $action->route('setting?id=' . $item['id']);
                $action->style('');
                $actions->add($action);
                $action = new Grid\Actions\ActionButton('卸载');
                $action->handler('request');
                $action->requestMethod('post');
                $action->uri(route('plugin/uninstall', ['id' => $item['id']]));
                $actions->add($action);
            }
        });
        return $grid;
    }

    /**
     * @param null $name
     * @return AbstractAdminPlugin
     * @throws BusinessException
     */
    public function getPlugin($name = null)
    {
        if ($name === null) $name = $this->request->query('id');
        if (empty($name)) {
            throw new BusinessException(403, 'id不能为空！');
        }
        $name = ucfirst($name);
        $class_name = 'App\\Plugins\\' . $name . '\\' . $name . "Plugin";
        if (class_exists($class_name)) {
            return new $class_name;
        } else {
            throw new BusinessException(403, '插件不存在！');
        }
    }
}