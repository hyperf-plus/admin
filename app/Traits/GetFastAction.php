<?php

namespace App\Traits;

use App\Entity\Principal;
use App\Model\Model;
use Exception;
use Hyperf\Database\Query\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;
use Mzh\Validate\Validate\Validate;
use Psr\Container\ContainerInterface;

trait GetFastAction
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct(ContainerInterface $container, RequestInterface $request)
    {
        $this->request = $request;
        $this->container = $container;
    }

    /**
     * @return array
     * @throws Exception
     * @throws \Exception
     */
    public function delete()
    {
        list($validate, $model, $data) = $this->fastModel();
        $data = $validate->makeCheck($data, 'del');
        $key = $model->getKeyName();
        IF (!is_array($data[$key])) {
            $data[$key] = [$data[$key]];
        }
        if ($model->delete($data[$key])) {
            return $this->json([], '操作成功！');
        } else {
            return $this->json([], '删除失败！', 0);
        }
    }


    /**
     * @return array
     * @throws Exception
     * @throws \Exception
     */
    public function update_top()
    {
        list($validate, $model, $data) = $this->fastModel();
        $data = $validate->makeCheck($data, 'top');
        $keyName = $model->getKeyName();
        $list = [];
        Db::beginTransaction();
        try {
            foreach ($data[$keyName] as $key => $item) {
                /** @var Builder $result */
                $result = $model::query(true)->find($item);
                if (!$result->exists()) {
                    throw new Exception('数据不存在');
                }
                $result->is_top = $data['is_top'];
                $result->save();
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
            return $this->error($e->getMessage());
        }
        return $this->json(true, '操作成功！');
    }

    /**
     * @return array
     * @throws Exception
     * @throws \Exception
     */
    public function update_index()
    {
        list($validate, $model, $data) = $this->fastModel();
        $data = $validate->makeCheck($data, 'index');
        $keyName = $model->getKeyName();
        $list = [];
        Db::beginTransaction();
        try {
            foreach ($data[$keyName] as $key => $item) {
                $result = $model::query(true)->find($item);
                /** @var Builder $result */
                if (!$result->exists()) {
                    throw new Exception('数据不存在');
                }
                $result->sort = $key + 1;
                $result->save();
            }
            Db::commit();
        } catch (\Exception $e) {
            p($e->getMessage());
            Db::rollBack();
            return $this->error('排序失败！');
        }
        return $this->json(true, '排序成功！');
    }

    /**
     * @return array
     * @throws Exception
     * @throws \Exception
     */
    public function update_sort()
    {
        /** @var Model $model */
        list($validate, $model, $data) = $this->fastModel();
        $data = $validate->makeCheck($data, 'sort');

        $key = $model->getKeyName();
        $result = $model::query(true)->find($data[$key]);
        if (!$result->exists()) {
            throw new Exception('选择的数据不存在！');
        }
        $result->sort = $data['sort'];
        $result->save();
        return $this->json([], '排序成功！');
    }

    /**
     * @return array
     * @throws Exception
     */
    public function info()
    {
        /** @var Model $model */
        list($validate, $model, $data) = $this->fastModel();
        $data = $validate->makeCheck($data, 'item');
        $key = $model->getKeyName();
        $result = $model::query()->find($data[$key]);
        if (!$result->exists()) {
            throw new Exception('数据不存在！');
        }
        return $this->json($result->toArray(), '操作成功！');
    }


    /**
     * @return array
     * @throws Exception
     */
    public function update_status()
    {
        /** @var Model $model */
        list($validate, $model, $data) = $this->fastModel();
        $data = $validate->makeCheck($data, 'status');
        $key = $model->getKeyName();

        $data['status'] ??= 1;

        switch (true) {
            case is_array($data[$key]):
                $result = $model::query(true)->find($data[$key]);
                if ($result->isEmpty()) {
                    throw new Exception('选择的数据不存在！');
                }
                foreach ($result as $item) {
                    $item->status = $data['status'];
                    $item->save();
                }
                break;
            default:
                $result = $model::query()->find($data[$key]);
                if (!$result->exists()) {
                    throw new Exception('选择的数据不存在！');
                }
                $result->status = $data['status'];
                $result->save();
                break;
        }
        return $this->json([], '操作成功！');
    }

    /**
     * @return array
     * @throws Exception
     */
    public function update()
    {
        /** @var Model $model */
        list($validate, $model, $data) = $this->fastModel();
        $data = $validate->makeCheck($data, 'set');
        $key = $model->getKeyName();
        $result = $model::query(true)->find($data[$key]);
        if (!$result->exists()) {
            throw new Exception('选择的数据不存在！');
        }
        if ($result->fill($data)->save()) {
            return $this->success($result->toArray());
        }
        return $this->error('操作失败！');
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function create()
    {
        // data 数据已经按照验证器过滤。已去掉多余数据，可以直接使用
        list($validate, $model, $data) = $this->fastModel();
        $data = $validate->makeCheck($data);
        $Created = $model::query()->create($data);
        if ($Created) {
            return $this->success($Created->toArray());
        }
        throw new Exception('添加失败！');
    }

    /**
     * @return array
     * @throws Exception
     */
    private function fastModel()
    {
        $className = class_basename(__CLASS__);
        $className = current(explode('_', $className)); //处理自动缓存后名字是缓存的名字问题
        /** * @var Model $model ; */
        /** * @var Validate $validate ; */
        $class = "\\App\\Model\\" . $className;
        if (!class_exists($class, false)) {
            throw new \Exception('模型不存在，无法使用快捷模式！');
        }
        $validate_class = "\\App\\Validate\\" . $className . "Validation";
        if (!class_exists($validate_class, false)) {
            throw new \Exception('验证模型不存在，无法使用快捷模式！');
        }
        $model = new $class;
        $validate = make($validate_class);
        $data = $this->request->getParsedBody();
        return [$validate, $model, $data];
    }


}
