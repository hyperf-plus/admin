<?php

namespace Mzh\DevTools\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Admin\Traits\GetApiBase;
use Mzh\Admin\Traits\GetUiBase;
use Mzh\DevTools\ControllerMaker;
use Mzh\DevTools\ModelMaker;
use Mzh\DevTools\TableSchema;
use Mzh\DevTools\ValidateMaker;
use Mzh\DevTools\ViewMaker;
use Mzh\DevTools\Views\DevView;

/**
 * @AutoController(prefix="/api/dev",tag="开发工具", description="开发工具")
 * Class IndexController
 * @package Mzh\Admin\Controller
 */
class Dev
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    use GetApiBase;
    use GetUiBase;

    public function maker()
    {
        $rule = (new DevView)->rules();
        $form = $this->formOptionsConvert($rule, false, false);
        $compute_map = $this->formComputeConfig($form);

        return $this->json([
            'form' => $form,
            'compute_map' => (object)$compute_map,
        ]);
    }

    public function validate()
    {
        $rule = (new DevView)->validate();
        $form = $this->formOptionsConvert($rule, false, false);
        $compute_map = $this->formComputeConfig($form);

        return $this->json([
            'form' => $form,
            'compute_map' => (object)$compute_map,
        ]);
    }

    public function controller()
    {
        $rule = (new DevView)->controller();
        $form = $this->formOptionsConvert($rule, false, false);
        $compute_map = $this->formComputeConfig($form);

        return $this->json([
            'form' => $form,
            'compute_map' => (object)$compute_map,
        ]);
    }


    public function make()
    {
        $data_source = $this->request->all();
        if ($data_source['make_type'] == 'controller') {
            $rule = (new DevView)->controller();
        } elseif ($data_source['make_type'] == 'validate') {
            $rule = (new DevView)->validate();
        } elseif ($data_source['make_type'] == 'maker') {
            $rule = (new DevView)->rules();
        } else {
            throw new ValidateException(1000, 'make_type 不能为空！');
        }

        $msg = [];
        $validate_class = '';
        $view_class = '';
        $model_class = '';
        $rules = $this->getFormRules($rule);
        [
            $data,
            $errors,
        ] = $this->check($rules, $data_source);
        if ($errors) {
            throw new \Exception(implode(PHP_EOL, $errors));
        }
        if ($data['make_type'] == 'maker') {
            /** @var ModelMaker $model_maker */
            $model_maker = make(ModelMaker::class);
            $model_class = $model_maker->make($data['pool'], $data['database'], $data['table'], $data['model_path']);
            if ($model_class === false) {
                throw new \Exception('Model创建失败');
            }
            $msg[] = $model_class;
        }

        if ($data['make_type'] == 'maker') {
            /** @var ViewMaker $view_maker */
            $view_maker = make(ViewMaker::class);
            $view_class = $view_maker->make($model_class, $data['view_path'], $data);
            if ($view_class === false) {
                throw new \Exception('View创建失败');
            }
            $msg[] = $view_class;
        }

        if ($data['make_type'] == 'maker' || $data['make_type'] == 'validate') {
            if (isset($data['validate_name'])) {
                $model_class = $data['validate_name'];
            }
            /** @var ValidateMaker $validate_maker */
            $validate_maker = make(ValidateMaker::class);
            $validate_class = $validate_maker->make($model_class, $data['validate_path'], $data);
            if ($validate_class === false) {
                throw new \Exception('Validate创建失败');
            }
            $msg[] = $validate_class;
        }
        if ($data['make_type'] == 'maker' || $data['make_type'] == 'controller') {
            /** @var ControllerMaker $ctl_maker */
            $ctl_maker = make(ControllerMaker::class);
            $controller_name = $ctl_maker->make($data['controller_name']??'', $model_class, $validate_class, $view_class, $data['controller_path'], $data);
            if ($controller_name === false) {
                throw new \Exception('Controller创建失败');
            }
            $msg[] = $controller_name;
        }

        $msg = ['创建成功'];
        return $this->json([], implode("\n", $msg));
    }

    public function dbAct()
    {
        $pool = $this->request->input('pool');
        /** @var TableSchema $tool */
        $tool = make(TableSchema::class);
        $dbs = $tool->getDbs($pool);
        $options = [];
        foreach ($dbs as $db) {
            $options[] = [
                'value' => $db,
                'label' => $db,
            ];
        }

        return $this->json($options);
    }

    public function tableAct()
    {
        $pool = $this->request->input('pool');
        $db = $this->request->input('db');
        /** @var TableSchema $tool */
        $tool = make(TableSchema::class);
        $dbs = $tool->databasesTables($pool, $db);
        $options = [];
        foreach ($dbs as $db) {
            $options[] = [
                'value' => $db,
                'label' => $db,
            ];
        }
        return $this->json($options);
    }

    public function tableSchema()
    {
        $pool = $this->request->input('pool');
        $db = $this->request->input('db');
        $table = $this->request->input('table');
        /** @var TableSchema $tool */
        $tool = make(TableSchema::class);
        $schema = $tool->tableSchema($pool, $db, $table);
        $ret = [];
        $ignores = ['id', 'create_at', 'update_at', 'is_deleted'];
        foreach ($schema as $item) {
            $newItem = new \stdClass();
            foreach ($item as $key => $value) {
                $key = strtolower($key);
                $newItem->$key = $value;
            }
            if (in_array($newItem->column_name, $ignores)) {
                continue;
            }
            $ret[] = [
                'field' => $newItem->column_name,
                'label' => $newItem->column_comment,
                'type' => $this->transType($newItem->data_type),
            ];
        }
        return $this->json($ret);
    }

    public function transType($type)
    {
        switch ($type) {
            case 'datetime':
                return 'datetime';
            case 'bigint':
                return 'number';
            default:
                return 'string';
        }
    }
}
