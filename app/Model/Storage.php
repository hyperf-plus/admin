<?php

namespace App\Model;

use App\Exception\AddException;
use App\Exception\FileException;
use Hyperf\DbConnection\Db;
use mysql_xdevapi\Exception;
use Symfony\Component\Mime\Exception\AddressEncoderException;

class Storage extends BaseModel
{
    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    protected $table = 'storage';
    protected $primaryKey = 'storage_id';
    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'storage_id',
        'hash'
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'storage_id' => 'integer',
        'parent_id' => 'integer',
        'size' => 'integer',
        'type' => 'integer',
        'sort' => 'integer',
        'pixel' => 'array',
        'is_default' => 'integer',
    ];

    /**
     * 添加一个资源目录
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addStorageDirectoryItem($data)
    {
        $this->validateData($data, 'Storage.add_directory');

        // 初始化数据
        $data['type'] = 2;
        $data['protocol'] = '';
        $data['priority'] = 0;
        $field = ['parent_id', 'name', 'type', 'protocol', 'priority', 'sort'];

        if (false !== $this->allowField($field)->save($data)) {
            Cache::clear('StorageDirectory');
            return $this->hidden(['protocol'])->toArray();
        }

        return false;
    }

    /**
     * 编辑一个资源目录
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setStorageDirectoryItem($data)
    {
        $this->validateSetData($data, 'Storage.set_directory');

        $map['storage_id'] = ['eq', $data['storage_id']];
        $map['type'] = ['eq', 2];

        if (false !== $this->allowField(['name', 'sort'])->save($data, $map)) {
            Cache::clear('StorageDirectory');
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取资源目录选择列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageDirectorySelect($data)
    {
        $this->validateData($data, 'Storage.list_directory');
        // 排序方式与排序字段
        $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';
        $orderField = !empty($data['order_field']) ? $data['order_field'] : 'storage_id';
        $order['sort'] = 'asc';
        $order[$orderField] = $orderType;

        if (!empty($data['order_field'])) {
            $order = array_reverse($order);
        }
        $model = self::where('type', 2);
        foreach ($order as $field => $type) {
            $model->orderBy($field, $type);
        }
        // 获取实际数据
        $result = $model->get(['storage_id', 'parent_id', 'name', 'cover', 'sort', 'is_default']);
        if ($result) {
            return [
                'list' => $result->toArray(),
                'default' => self::getDefaultStorageId(),
            ];
        }
        throw new FileException('获取失败');
    }

    /**
     * 将资源目录标设为默认目录
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setStorageDirectoryDefault($data)
    {
        if (!$this->validateData($data, 'Storage.default')) {
            return false;
        }

        $map['type'] = ['eq', 2];
        if (false === $this->save(['is_default' => 0], $map)) {
            return false;
        }

        $map['storage_id'] = ['eq', $data['storage_id']];
        if (false === $this->save(['is_default' => $data['is_default']], $map)) {
            return false;
        }

        Cache::clear('StorageDirectory');
        return true;
    }

    /**
     * 获取一个资源或资源目录
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageItem($data)
    {
        if (!$this->validateData($data, 'Storage.item')) {
            return false;
        }

        $result = self::get($data['storage_id']);
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取资源列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageList($data)
    {
        if (!$this->validateData($data, 'Storage.list')) {
            return false;
        }

        // 初始化数据
        $data['storage_id'] = !is_empty_parm($data['storage_id']) ? $data['storage_id'] : 0;

        // 搜索条件
        $map = [];
        $map['parent_id'] = ['eq', $data['storage_id']];

        if (!empty($data['name'])) {
            $map['name'] = ['like', '%' . $data['name'] . '%'];
            $map['storage_id'] = ['neq', $data['storage_id']];
            unset($map['parent_id']);
        }

        // 获取总数量,为空直接返回
        $totalResult = $this->where($map)->count();
        if ($totalResult <= 0) {
            return ['total_result' => 0];
        }

        $result = self::all(function ($query) use ($data, $map) {
            // 翻页页数
            $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

            // 每页条数
            $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

            // 排序方式
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';

            // 排序的字段
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'storage_id';

            // 排序处理
            $order['priority'] = 'asc';
            $order[$orderField] = $orderType;

            $query
                ->where($map)
                ->order($order)
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            return ['items' => $result->toArray(), 'total_result' => $totalResult];
        }

        return false;
    }

    /**
     * 获取导航数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getStorageNavi($data)
    {
        if (!$this->validateData($data, 'Storage.navi')) {
            return false;
        }

        if (empty($data['storage_id'])) {
            return [];
        }

        $map['type'] = ['eq', 2];
        $list = self::cache('StorageNavi', null, 'StorageDirectory')->where($map)->column('storage_id,parent_id,name');

        if ($list === false) {
            Cache::clear('StorageDirectory');
            return false;
        }

        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;
        if (!$isLayer && isset($list[$data['storage_id']])) {
            $data['storage_id'] = $list[$data['storage_id']]['parent_id'];
        }

        $result = [];
        while (true) {
            if (!isset($list[$data['storage_id']])) {
                break;
            }

            $result[] = $list[$data['storage_id']];

            if ($list[$data['storage_id']]['parent_id'] <= 0) {
                break;
            }

            $data['storage_id'] = $list[$data['storage_id']]['parent_id'];
        }

        return array_reverse($result);
    }

    /**
     * 重命名一个资源
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function renameStorageItem($data)
    {
        if (!$this->validateData($data, 'Storage.rename')) {
            return false;
        }

        $map['storage_id'] = ['eq', $data['storage_id']];
        if (false !== $this->save(['name' => $data['name']], $map)) {
            Cache::clear('StorageDirectory');
            return $this->toArray();
        }

        return false;
    }

    /**
     * 将某张图片资源设为目录或视频封面
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setStorageCover($data)
    {
        if (!$this->validateData($data, 'Storage.cover')) {
            return false;
        }

        $result = self::get(function ($query) use ($data) {
            $map['storage_id'] = ['eq', $data['storage_id']];
            $map['type'] = ['eq', 0];

            $query->where($map);
        });

        if (!$result) {
            return is_null($result) ? $this->setError('资源图片不存在') : false;
        }

        $coverMap['storage_id'] = ['eq', $data['parent_id']];
        $coverMap['type'] = ['in', [2, 3]];

        if (false !== $this->save(['cover' => $result->getAttr('url')], $coverMap)) {
            Cache::clear('StorageDirectory');
            return true;
        }

        return false;
    }

    /**
     * 清除目录资源的封面
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function clearStorageCover($data)
    {
        if (!$this->validateData($data, 'Storage.clear_cover')) {
            return false;
        }

        $map['storage_id'] = ['eq', $data['storage_id']];
        if (false !== $this->save(['cover' => ''], $map)) {
            Cache::clear('StorageDirectory');
            return true;
        }

        return false;
    }

    /**
     * 批量移动资源到指定目录
     * @access public
     * @param array $data 外部数据
     * @return mixed
     * @throws
     */
    public function moveStorageList($data)
    {
        if (!$this->validateData($data, 'Storage.move')) {
            return false;
        }

        // 开启事务
        self::startTrans();

        try {
            $data['storage_id'] = array_unique($data['storage_id']);
            $rootId = self::where('storage_id', $data['storage_id'][0])->value('parent_id'); // 原来的父级

            // 不需要任何移动操作
            if ($data['parent_id'] == $rootId) {
                return [];
            }

            // 防止自身移动到自身
            $posNode = array_search($data['parent_id'], $data['storage_id']);
            if (false !== $posNode) {
                unset($data['storage_id'][$posNode]);
            }

            if (0 != $data['parent_id']) {
                $parentResult = self::get($data['parent_id']); // 新的父级
                if (!$parentResult) {
                    throw new \Exception('上级资源目录不存在');
                }

                // 将原来的子级处理为新的父级目录
                if (in_array($parentResult['parent_id'], $data['storage_id'])) {
                    $parentResult->save(['parent_id' => $rootId]);
                }
            }

            $map['storage_id'] = ['in', $data['storage_id']];
            if (false === $this->save(['parent_id' => $data['parent_id']], $map)) {
                throw new \Exception($this->getError());
            }

            self::commit();
            Cache::clear('StorageDirectory');

            sort($data['storage_id']);
            return $data['storage_id'];
        } catch (\Exception $e) {
            self::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 批量删除资源
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delStorageList($data)
    {
        if (!$this->validateData($data, 'Storage.del')) {
            return false;
        }

        // 数组转为字符串格式,用于SQL查询条件,为空直接返回
        $delList['storage_id'] = array_unique($data['storage_id']);
        $delList['storage_id'] = implode(',', $delList['storage_id']);

        if (empty($delList['storage_id'])) {
            return true;
        }

        // 获取子节点资源
        $storageId = $this->query('SELECT `getStorageChildrenList`(:storage_id) AS `storage_id`', $delList);
        if (false === $storageId) {
            return false;
        }

        // 获取所有资源数据(不可使用FIND_IN_SET查询,不走索引,效率极低)
        $result = $this->where(['storage_id' => ['in', $storageId[0]['storage_id']]])->select();
        if ($result->isEmpty()) {
            return true;
        }

        $delDirId = [];
        $result = $result->toArray();
        $ossObjectList = new \StdClass();

        foreach ($result as $value) {
            // 如果是资源目录则加入待删除列表
            if ($value['type'] == 2) {
                $delDirId[] = $value['storage_id'];
                continue;
            }

            if ($value['type'] != 2 && !empty($value['protocol'])) {
                if (!isset($ossObjectList->oss[$value['protocol']])) {
                    $ossObject = new Upload();
                    $ossObjectList->oss[$value['protocol']] = $ossObject->createOssObject($value['protocol']);

                    if (false === $ossObjectList->oss[$value['protocol']]) {
                        return $this->setError($ossObject->getError());
                    }
                }

                $ossObjectList->oss[$value['protocol']]->addDelFile($value['path']);
                $ossObjectList->oss[$value['protocol']]->addDelFileId($value['storage_id']);
            }
        }

        // 开启事务
        self::startTrans();

        try {
            if (isset($ossObjectList->oss)) {
                foreach ($ossObjectList->oss as $item) {
                    // 删除OSS物理资源
                    if (false === $item->delFileList()) {
                        throw new \Exception($item->getError());
                    }

                    // 删除资源记录
                    $this->where(['storage_id' => ['in', $item->getDelFileIdList()]])->delete();
                }
            }

            // 删除资源目录记录
            if (!empty($delDirId)) {
                $this->where(['storage_id' => ['in', $delDirId]])->delete();
            }

            self::commit();
            Cache::clear('StorageDirectory');
            return true;
        } catch (\Exception $e) {
            self::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 清除图片资源缓存
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function clearStorageThumb($data)
    {
        if (!$this->validateData($data, 'Storage.thumb')) {
            return false;
        }

        $result = self::get(function ($query) use ($data) {
            $map['storage_id'] = ['eq', $data['storage_id']];
            $map['type'] = ['eq', 0];

            $query->where($map)->field('path,protocol,url');
        });

        if (!$result) {
            return is_null($result) ? $this->setError('资源图片不存在') : false;
        }

        $url = parse_url($result['url']);
        $newUrl = sprintf('%s?type=%s&rand=%s', $url['path'], $result['protocol'], mt_rand(0, time()));

        if (false === $result->save(['url' => $newUrl])) {
            return $this->setError($this->getError());
        }

        $path = ROOT_PATH . 'public' . $result['path'];
        $path = str_replace(IS_WIN ? '/' : '\\', DS, $path);

        $ossObject = (new Upload())->createOssObject($result['protocol']);
        $ossObject->clearThumb($path);

        return true;
    }

    /**
     * 获取默认目录的资源编号
     * @access public
     * @return integer
     */
    public static function getDefaultStorageId()
    {
        $map['type'] = 2;
        $map['is_default'] = 1;
        $result = self::where($map)
            ->value('storage_id');
        return $result ? $result : 0;
    }
}
