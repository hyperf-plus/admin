<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use MZH\Admin\Service\ServiceInterface;
use Mzh\Admin\Views\UiViewInterface;
use Mzh\Helper\DbHelper\QueryHelper;
use Mzh\Validate\Validate\Validate;

trait GetApiBase
{
    protected $service;
    protected $model = null;
    protected $validate = null;
    protected $view = null;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;


    public function __construct()
    {
        $this->request = request();
        $this->response = response();
    }

    /**
     * @param null $model
     * @return Builder
     */
    protected function getModel()
    {
        if (!empty($this->model)) {
            return $this->model;
        }
        if (property_exists($this, 'modelClass') && $this->modelClass) {
            $this->model = new $this->modelClass;
        }
        if (empty($this->model)) {
            $model = class_basename(__CLASS__);
            /** * @var Model $model ; */
            /** * @var Validate $validate ; */
            $class = "\\Mzh\\Admin\\Model\\" . $model;
            if (!class_exists($class)) {
                throw new ValidateException(1000, $model . '模型不存在，请先创建db模型！');
            }
            $this->model = make($class);
        }
        return $this->model;
    }

    protected function getView(): UiViewInterface
    {
        if (!empty($this->view)) {
            return $this->view;
        }
        if (property_exists($this, 'viewClass') && $this->viewClass) {
            $this->view = new $this->viewClass;
        }
        if (empty($this->view)) {
            $model = class_basename(__CLASS__);
            /** * @var Validate $validate ; */
            $class = "\\Mzh\\Admin\\Views\\" . $model . "View";
            if (!class_exists($class)) {
                throw new ValidateException(1000, $model . 'View不存在，请先创建' . $class . '！');
            }
            $this->view = new $class;
        }
        return $this->view;
    }

    protected function getValidate()
    {
        if (!empty($this->validate)) {
            return $this->validate;
        }
        if (property_exists($this, 'validateClass') && $this->validateClass) {
            $this->validate = new $this->validateClass;
        }
        if (empty($this->validate)) {
            $model = class_basename(__CLASS__);
            /** * @var Validate $validate ; */
            $class = "\\Mzh\\Admin\\Validate\\" . $model . "Validation";
            if (!class_exists($class)) {
                throw new ValidateException(1000, $model . '验证器不存在，请先创建验证器' . $class . '！');
            }
            $this->validate = new $class;
        }
        return $this->validate;
    }

    protected function getService()
    {
        if ($this->service instanceof ServiceInterface) {
            return $this->service;
        }
        if ($this->serviceClass) {
            $this->service = make($this->serviceClass);
        }
        return $this->service;
    }

    /**
     * 数据回调处理机制
     * @param string $name 回调方法名称
     * @param mixed $one 回调引用参数1
     * @param mixed $two 回调引用参数2
     * @return boolean
     */
    protected function callback($name, &$one = [], &$two = [])
    {
        $paths = explode('/', $this->request->getUri()->getPath());
        if (is_callable($name)) return call_user_func($name, $this, $one, $two);
        foreach ([$name, "_" . end($paths) . "{$name}"] as $method) {
            if (method_exists($this, $method) && false === $this->$method($one, $two)) {
                return false;
            }
        }
        return true;
    }

    protected function json($data = [], $msg = '')
    {
        $res = [];
        if (is_string($data)) {
            $res['_message'] = $data;
        } else {
            $res = $data;
            if (!empty($msg)) $res['_message'] = $msg;
        }
        return response()->json($res);
    }


    /**
     * @param Model|QueryHelper $model
     * @param null $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function _list($model = null, $data = null)
    {
        if ($model instanceof QueryHelper) {
            $query = $model;
        } else {
            if (empty($model)) $model = $this->getModel();
            if (empty($data)) $data = $this->request->getParsedBody();
            $query = $this->QueryHelper($model, $data);
        }
        $this->callback('_before', $query);
        return $query->paginate(['*'], function ($list) {
            $this->callback('_after', $list);
            return $list;
        });
    }

    /**
     * @param array $data
     * @param null $model
     * @param null $validate
     * @return array
     */
    protected function _create(array $data = [], $model = null, $validate = null)
    {
        // data 数据已经按照验证器过滤。已去掉多余数据，可以直接使用
        $data = $this->_checkData($data, $validate, trim(__FUNCTION__, '_'));
        $this->callback('_before', $data);
        if (empty($model)) $model = $this->getModel();
        $Created = $model::create($data);
        $this->callback('_after', $Created);
        if ($Created) {
            return $Created->toArray();
        }
        throw new BusinessException(1000, '创建失败');
    }

    protected function _updateOrCreate(array $data = [], $model = null, $pk = null, $validate = null)
    {
        if (empty($model)) $model = $this->getModel();
        if (empty($pk)) $pk = $model->getKeyName();
        $data = $this->request->all();
        if (!isset($data[$pk]) || (isset($data[$pk]) && empty($data[$pk]))) {
            return $this->_create($data, $model, $validate);
        } else {
            return $this->_update($data[$pk], $data, $model, $pk, $validate);
        }
    }

    /**
     * @param null $pkVal
     * @param array $data
     * @param null $model
     * @param null $pk
     * @param null $validate
     * @return array
     */
    protected function _update($pkVal = null, array $data = [], $model = null, $pk = null, $validate = null)
    {
        // data 数据已经按照验证器过滤。已去掉多余数据，可以直接使用
        $data = $this->_checkData($data, $validate, trim(__FUNCTION__, '_'));
        if (empty($model)) $model = $this->getModel();
        if (empty($pk)) $pk = $model->getKeyName();
        $this->callback('_before', $data);
        if ($pkVal === null && empty($data[$pk])) {
            throw new ValidateException(1000, $pk . '不能为空');
        }
        $info = $model->where($model->getTable() . '.' . $pk, (!empty($pkVal) ? $pkVal : $data[$pk]))->first();
        if (empty($info)) {
            throw new BusinessException(1000, '数据不存在');
        }

        if ($info->fill($data)->save()) {
            $this->callback('_after', $info);
            return $info->toArray();
        }
        throw new BusinessException(1000, '更新失败');
    }


    /**
     * @param string $field
     * @param string $pk
     * @param Model $model
     * @return array|string
     */
    protected function _field($field = 'status', $pk = null, $model = null)
    {
        if (empty($model)) $model = $this->getModel();
        if (empty($pk)) $pk = $model->getKeyName();
        $this->callback('_before', $data);
        $data = $this->request->all();
        if (!isset($data[$pk]) || empty($data[$pk])) {
            throw new ValidateException(1000, $pk . '不能为空');
        }
        if (!isset($data[$field])) {
            throw new ValidateException(1000, '更新字段值不能为空');
        }
        if (is_array($data[$pk])) {
            $count = $model->whereIn($model->getTable() . '.' . $pk, $data[$pk])->update([
                $field => $data[$field]
            ]);
            if ($count > 0) {
                $info = '成功更新' . $count . '条数据';
                $this->callback('_after', $info);
                return $info;
            }
        } else {
            $info = $model->where($model->getTable() . '.' . $pk, $data[$pk])->first();
            if (empty($info)) {
                throw new BusinessException(1000, '数据不存在');
            }
            $info->$field = $data[$field];
            if ($info->save()) {
                $this->callback('_after', $info);
                return [$field => $info->$field];
            }
        }
        throw new BusinessException(1000, '更新失败');
    }

    /**
     * @param null $val
     * @param string $pk
     * @param null $model
     * @return bool
     */
    protected function _delete($val = null, $pk = 'id', $model = null)
    {
        // data 数据已经按照验证器过滤。已去掉多余数据，可以直接使用
        if (empty($model)) $model = $this->getModel();
        if (empty($pk)) $pk = $model->getKeyName();
        if (empty($val)) {
            $data = $this->request->all();
            $val = $data[$pk] ?? null;
        };
        $this->callback('_before', $data);
        if (empty($val)) {
            throw new ValidateException(1000, $pk . '不能为空');
        }
        if (is_array($val)) {
            $model = $model->whereIn($pk, $val);
        } else {
            $model = $model->where($pk, $val);
        }
        if ($model->delete()) {
            $this->callback('_after', $val);
            return true;
        }
        throw new BusinessException(1000, '删除失败');
    }

    /**
     * @param null $id
     * @param null $model
     * @param null $pk
     * @return array
     */
    protected function _detail($id = null, $pk = null, $model = null)
    {
        // data 数据已经按照验证器过滤。已去掉多余数据，可以直接使用
        if (empty($model)) $model = $this->getModel();
        if (empty($pk)) $pk = $model->getKeyName();
        if (empty($id)) {
            $data = $this->request->all();
            $id = $data[$pk] ?? null;
        }
        $this->callback('_before', $data);
        if (empty($id)) {
            throw new ValidateException(1000, $pk . '不能为空');
        }
        $info = $model->where($model->getTable() . '.' . $pk, $id)->first();
        if (empty($info)) {
            throw new BusinessException(1000, '数据不存在');
        }
        $this->callback('_after', $info);
        return $info->toArray();
    }


    /**
     * @param $data
     * @param $validate
     * @param $scene
     * @return array|object|null
     * @throws  ValidateException
     */
    protected function _checkData($data, $validate, $scene)
    {
        if (empty($data)) {
            $data = $this->request->all();
        }
        if ($validate === false) return $data;
        if (empty($validate)) $validate = $this->getValidate();
        if (!$validate->scene($scene)->check($data)) {
            throw new ValidateException(1000, $validate->getError());
        }
        $rules = $validate->getSceneRule($scene);
        $fields = [];
        foreach ($rules as $field => $rule) {
            if (is_numeric($field)) {
                $field = $rule;
            }
            $fields[$field] = 1;
        }
        foreach ($data as $key => $item) {
            if (!isset($fields[$key]) && $key != $this->getPk()) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * 获取当前操作entity的主键
     *
     * @return string
     */
    protected function getPk()
    {
        try {
            return $this->getModel()->getKeyName();
        } catch (ValidateException $exception) {
            return 'id';
        }
    }
}