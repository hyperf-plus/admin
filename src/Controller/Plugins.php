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
use HPlus\Admin\Model\Plugin;
use HPlus\Admin\Service\PluginService;
use HPlus\Route\Annotation\AdminController;
use HPlus\Route\Annotation\GetApi;
use HPlus\Route\Annotation\PostApi;
use HPlus\Route\Annotation\Query;
use HPlus\UI\Components\Form\CSwitch;
use HPlus\UI\Components\Form\Input;
use HPlus\UI\Components\Grid\Tag;
use HPlus\UI\Components\Widgets\Dialog;
use HPlus\UI\Form;
use HPlus\UI\Form\FormActions;
use HPlus\UI\Grid;
use HPlus\UI\Layout\Content;
use HPlus\UI\UI;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * @AdminController(prefix="plugin", tag="插件管理", ignore=true))
 */
class Plugins extends AbstractAdminController
{
    /**
     * @var PluginService
     */
    private $pluginService;

    public function __construct(ContainerInterface $container, RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($container, $request, $response);
        $this->pluginService = $container->get(PluginService::class);
    }

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
        if (! $pluginModel) {
            throw new BusinessException(403, '插件未安装');
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
        if (! $form instanceof Form) {
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
            throw new BusinessException(403, '该插件已安装');
        }
        if (! $pluginClass->install()) {
            throw new BusinessException(403, '安装失败');
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
        return UI::responseMessage('安装成功');
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
        if (! $plugin) {
            throw new BusinessException(403, '该插件未安装');
        }
        $pluginClass = $this->getPlugin($plugin->name);
        if (! $pluginClass->uninstall()) {
            throw new BusinessException(403, '卸载失败,插件文件不存在');
        }
        $plugin->delete();
        return UI::responseMessage('卸载成功');
    }

    public function data()
    {
        $listPlugin = AnnotationCollector::getClassesByAnnotation(AdminPlugins::class);
        $listPlugin = collect($listPlugin)->keyBy('name');
        $list = Plugin::get()->keyBy('name');
        $list = $listPlugin->merge($list);
        return $list->toArray();
    }

    /**
     * @param null $name
     * @throws BusinessException
     * @return AbstractAdminPlugin
     */
    public function getPlugin($name = null)
    {
        if ($name === null) {
            $name = $this->request->query('id');
        }
        if (empty($name)) {
            throw new BusinessException(403, 'id不能为空！');
        }
        $name = ucfirst($name);
        $class_name = 'App\\Plugins\\' . $name . '\\' . $name . 'Plugin';
        if (class_exists($class_name)) {
            return new $class_name();
        }
        throw new BusinessException(403, '插件不存在！');
    }

    protected function grid()
    {
        $grid = new Grid();
        $grid
            ->stripe(true)->emptyText('暂无插件')
            ->perPage(10)
            ->hidePage();
        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->hideCreateButton();
            $tool = Grid\Tools\ToolButton::make('更新插件');
            $toolbars->addLeft($tool);
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
            [$check, $error] = $this->pluginService->checkComposer($plugin->composer());
            if (! $check) {
                $action = new Grid\Actions\ActionButton('依赖未安装');
                $action->dialog(function (Dialog $dialog) use ($error) {
                    $dialog->title('检测依赖失败');
                    $dialog->slot(function (Content $content) use ($error) {
                        /**
                         * @var Form $form
                         */
                        #todo 暂时先用form做详情查看，后续做view组件的话在进行更改
                        $form = new Form();
                        $form->isGetData(false);
                        $form->actions(function (FormActions $actions) {
                            $actions->hideCancelButton();
                            $actions->hideSubmitButton();
                        });
                        if (! empty($error['noInstall'])) {
                            $form->item('help_install', '命令')->hideLabel()->defaultValue('以下依赖尚未安装，请复制编辑框内容执行安装')
                                ->component(Tag::make()->type('warning'));
                            $i = 0;
                            foreach ($error['noInstall'] as $item) {
                                ++$i;
                                $form->item('install' . $i, $i . '、')
                                    ->defaultValue('composer require ' . $item['package'] . ':' . $item['version']);
                            }
                        }
                        if (! empty($error['versionErr'])) {
                            $form->item('help', '命令')->hideLabel()->defaultValue('以下依赖版本不正确，请安装')
                                ->component(Tag::make()->type('warning'));
                            $i = 0;
                            foreach ($error['versionErr'] as $item) {
                                ++$i;
                                $form->item('package' . $i, $i . '、')
                                    ->defaultValue('composer require ' . $item['package'] . ':' . $item['need_version']);
                            }
                        }
                        $content->body($form);
                    });
                });
                $actions->add($action);
                return;
            }
            if (! isset($item['id'])) {
                $action = new Grid\Actions\ActionButton('安装');
                $action->dialog(function (Dialog $dialog) use ($plugin, $id) {
                    $dialog->title('安装插件');
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
                        $form->successRefData('tableReload');
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
}
