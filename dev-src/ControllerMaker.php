<?php

namespace Mzh\DevTools;

use Mzh\Admin\Controller\AbstractController;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Traits\GetApiAction;
use Mzh\Admin\Traits\GetApiBatchDel;
use Mzh\Admin\Traits\GetApiCreate;
use Mzh\Admin\Traits\GetApiDelete;
use Mzh\Admin\Traits\GetApiList;
use Mzh\Admin\Traits\GetApiSort;
use Mzh\Admin\Traits\GetApiState;
use Mzh\Admin\Traits\GetApiUI;
use Mzh\Admin\Traits\GetApiUpdate;
use Mzh\Helper\DbHelper\QueryHelper;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\DeleteApi;
use Mzh\Swagger\Annotation\GetApi;
use Mzh\Swagger\Annotation\Path;
use Mzh\Swagger\Annotation\PostApi;
use Mzh\Swagger\Annotation\PutApi;
use Mzh\Swagger\Annotation\Query;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpNamespace;

class ControllerMaker extends AbstractMaker
{
    /**
     * @param $controller_name
     * @param $model_class
     * @param $validate_class
     * @param $view_class
     * @param $path
     * @param $config
     * @return bool|string
     */
    public function make($controller_name, $model_class, $validate_class, $view_class, $path, $config)
    {
        $swagger_name = $controller_name . '模块';
        #是否为单独创建控制器？
        if ($config['make_type'] == 'maker') {
            $arr = explode('\\', $model_class);
            $model_name = end($arr);
            $controller_name = $model_name;
        } else {
            if (!$this->checkClassExists($validate_class, $controller_name)) {
                $validate_class = '';
            }

            if (!$this->checkClassExists($model_class, $controller_name)) {
                $model_class = '';
            }

            if (!$this->checkClassExists($view_class, $controller_name)) {
                $view_class = '';
            }
        }
        $class_namespace = $this->pathToNamespace($path);

        $save_path = BASE_PATH . '/' . $path . '/' . $controller_name . '.php';
        if (file_exists($save_path)) {
            p("文件已存在，跳过生成！");
            throw new BusinessException(1000, $controller_name . '文件已存在！');
        }

        /** @var ClassType $class */
        /** @var PhpNamespace $namespace */
        [
            $namespace,
            $class,
        ] = $this->getBaseClass($save_path, $class_namespace, $controller_name, AbstractController::class);

        if (isset($config['swagger_name']) && !empty($config['swagger_name'])) {
            $swagger_name = $config['swagger_name'];
        }
        $class->addComment('@ApiController(tag="' . $swagger_name . '")');
        $namespace->addUse(\Mzh\Swagger\Annotation\ApiController::class);

        #单独生成的控制器，需要检测是否存在验证器和model 和view
        if ($validate_class != '') {
            $namespace->addUse($validate_class);
            $arr = explode('\\', $validate_class);
            $class->addProperty('validateClass', new Literal(end($arr) . '::class'));
        }

        if ($view_class != '') {
            $namespace->addUse($view_class);
            $arr = explode('\\', $view_class);
            $class->addProperty('viewClass', new Literal(end($arr) . '::class'));
        }

        if ($model_class != '') {
            $namespace->addUse($model_class);
            $class->addProperty('modelClass', new Literal('Admin' . $model_name . '::class'));
        }

        $config['api_init'] = (array)array_filter($config['api_init']);
        $isUseAll = false;

        if (count($config['api_init']) == 9) {
            $namespace->addUse(Query::class);
            $namespace->addUse(Body::class);
            $namespace->addUse(Path::class);
            $namespace->addUse(PutApi::class);
            $namespace->addUse(DeleteApi::class);
            $namespace->addUse(PostApi::class);
            $namespace->addUse(GetApi::class);
            $namespace->addUse(GetApiAction::class);
            $class->addTrait(GetApiAction::class);
            $isUseAll = true;
        } else {
            foreach ($config['api_init'] as $item) {
                $namespace->addUse("\\Mzh\\Admin\\Traits\\" . $item);
                $class->addTrait("\\Mzh\\Admin\\Traits\\" . $item);
            }
        }

        foreach ($config['init_hooks'] as $item) {
            $class->addMethod($item)->setParameters($this->hooksParameter($item, $class, $namespace, $isUseAll))->addComment($this->hookName($item));
        }

        $code = $this->getNamespaceCode($namespace);
        if (file_put_contents($save_path, $code) === false) {
            return false;
        }
        return $class_namespace . '\\' . $controller_name;
    }

    public function hookName($hook_name)
    {
        switch ($hook_name) {
            case '_list_before':
                return "列表查询前操作，这里可用于筛选条件添加、也可在此做权数据权限二次验证等";
                break;
            case '_list_after':
                return "列表查询后操作，这里可用于列表数据二次编辑";
                break;
            case 'meddleFormRule':
            case 'beforeFormResponse':
                return '';
                break;
            case '_update_before':
                return "更新前操作，这里可以处理提交的参数\n这里的参数是已经经过验证器验证的\n也可在此做权数据权限二次验证等";
                break;
            case '_update_after':
                return "更新后操作，这里可以修改返回的值";
                break;
            case '_create_before':
                return "创建前操作，这里可以处理提交的参数\n这里的参数是已经经过验证器验证的\n也可在此做权数据权限二次验证等";
            case '_create_after':
                return "创建后操作，这里可以修改返回的值、例如隐藏字段等操作";
                break;
            case '_delete_before':
                return "删除前操作\n也可在此做权数据权限二次验证等";
            case '_delete_after':
                return "删除后操作，这里可以修改返回的值";
                break;
            case '_state_before':
                return "排序前操作\n也可在此做权数据权限二次验证等";
            case '_state_after':
                return "状态修改后操作，例如删除缓存等";
                break;
            case '_detail_before':
                return "单条信息查询前操作\n也可在此做权数据权限二次验证等";
            case '_detail_after':
                return "详情查询后回调，这里可以修改返回的值、例如隐藏字段等操作";
                break;
            case '_sort_before':
                return "排序前操作\n也可在此做权数据权限二次验证等";
                break;
        }
    }

    /**
     * @param $hook_name
     * @param $class
     * @param $namespace
     * @param bool $isUseAll
     * @return array|void
     */
    public function hooksParameter($hook_name, $class, $namespace, $isUseAll = false)
    {
        /** @var ClassType $class */
        /** @var PhpNamespace $namespace */
        $map = [
            'beforeInfo' => [
                (new Parameter('info'))->setReference(),
            ],
            '_list_before' => [
                (new Parameter('query'))->setReference()->setType(QueryHelper::class),
            ],
            '_list_after' => [
                (new Parameter('list'))->setReference(),
            ],
            '_state_before' => [
                (new Parameter('data'))->setReference(),
            ],
            '_state_after' => [
                (new Parameter('data'))->setReference(),
            ],
            'meddleFormRule' => [
                (new Parameter('id')),
                (new Parameter('form_rule'))->setReference(),
            ],
            'beforeFormResponse' => [
                (new Parameter('id')),
                (new Parameter('record'))->setReference(),
            ],
            '_update_before' => [
                (new Parameter('data'))->setReference(),
            ],
            '_sort_before' => [
                (new Parameter('data'))->setReference(),
            ],
            '_delete_before' => [
                (new Parameter('id')),
                (new Parameter('data'))->setReference(),
            ],
            '_delete_after' => [
                (new Parameter('data'))->setReference(),
            ],
            '_update_after' => [
                (new Parameter('data'))->setReference(),
            ],
            '_create_before' => [
                (new Parameter('data'))->setReference(),
            ],
            '_create_after' => [
                (new Parameter('data'))->setReference(),
            ],
            '_detail_before' => [
                (new Parameter('data'))->setReference(),
            ],
            '_detail_after' => [
                (new Parameter('detail'))->setReference(),
            ]
        ];
        if ($isUseAll) return [];

        switch ($hook_name) {
            case '_list_before':
            case '_list_after':
                $class->addTrait(GetApiList::class);
                $namespace->addUse(GetApi::class);
                $namespace->addUse(Query::class);
                break;
            case 'meddleFormRule':
            case 'beforeFormResponse':
                $class->addTrait(GetApiUI::class);
                break;
            case '_update_before':
            case '_update_after':
                $namespace->addUse(Body::class);
                $namespace->addUse(PutApi::class);
                $class->addTrait(GetApiUpdate::class);
                break;
            case '_create_before':
            case '_create_after':
                $namespace->addUse(Body::class);
                $namespace->addUse(PostApi::class);
                $class->addTrait(GetApiCreate::class);
                break;
            case '_delete_before':
            case '_delete_after':
                $namespace->addUse(Body::class);
                $namespace->addUse(DeleteApi::class);
                $class->addTrait(GetApiDelete::class);
                $class->addTrait(GetApiBatchDel::class);
                break;
            case '_state_before':
            case '_state_after':
                $namespace->addUse(Body::class);
                $namespace->addUse(PostApi::class);
                $namespace->addUse(GetApiState::class);
                break;
            case '_detail_before':
            case '_detail_after':
                $namespace->addUse(GetApi::class);
                $namespace->addUse(Query::class);
                $class->addTrait(GetApiState::class);
                break;
            case '_sort_before':
                $namespace->addUse(Body::class);
                $namespace->addUse(PostApi::class);
                $class->addTrait(GetApiSort::class);
                break;
        }
        return $map[$hook_name] ?? [];
    }

    private function checkClassExists($class, $controller_name)
    {
        if ($class == '' || !class_exists($class)) {
            if (empty($controller_name)) {
                return false;
            }
            return $this->checkClassExists($class . '/' . $controller_name, '');
        }
        return true;
    }
}
