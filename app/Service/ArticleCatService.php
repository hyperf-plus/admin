<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\ArticleCat;
use Exception;
use Hyperf\Database\Query\Builder;
use Hyperf\DbConnection\Db;
use Mzh\Validate\Annotations\Validation;

class ArticleCatService
{

    /**
     * @Validation(mode="ArticleCat",scene="list",field="data",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function list(array $data)
    {
        $data['article_cat_id'] ??= 0;
        $data['is_navi'] ??= null;
        $data['is_layer'] ??= true;
        $data['level'] ??= null;
        $db = Db::table('Article')->groupBy('article_cat_id')->select([
            'article_cat_id',
            Db::raw('count(*) as num')
        ]);
        $result = ArticleCat::query(true)->from('article_cat as c')->
        leftJoin('article_cat as s', function ($join) {
            $join->on('s.parent_id', '=', 'c.article_cat_id');
        })->leftJoinSub($db, 'cs_a', function (Builder $join) {
            $join->on('a.article_cat_id', '=', 'c.article_cat_id');
        })->where(function ($query) use ($data) {
            /** * @var Builder $query ; */
            // 子查询,查询关联的文章数量
            is_null($data['is_navi']) ?: $query->where('c.is_navi', $data['is_navi']);
        })->groupBy('c.article_cat_id')
            ->orderBy('c.parent_id')
            ->orderBy('c.sort')
            ->orderBy('c.article_cat_id')->get(['c.*', Db::raw('count(cs_s.article_cat_id) as children_total'), Db::raw('ifnull(cs_a.num, 0) as  aricle_total')]);
        $tree = [];
        if ($result->isEmpty()) {
            return [];
        }
        // 处理原始数据至菜单数据
        $tree = self::setArticleCatTree($tree, $data['article_cat_id'], $result, $data['level'], $data['is_layer']);
        return $tree;
    }

    /**
     * 过滤和排序所有分类
     * @access private
     * @param int $parentId 上级分类Id
     * @param object $list 原始模型对象
     * @param int $limitLevel 显示多少级深度 null:全部
     * @param bool $isLayer 是否返回本级分类
     * @param int $level 分类深度
     * @return array
     */
    private static function setArticleCatTree(&$tree, $parentId, &$list, $limitLevel = null, $isLayer = false, $level = 0)
    {
        $parentId != 0 ?: $isLayer = false; // 返回全部分类不需要本级
        foreach ($list as $key => $value) {
            // 获取分类主Id
            $articleCatId = $value->getAttribute('article_cat_id');
            if ($value->getAttribute('parent_id') !== $parentId && $articleCatId !== $parentId) {
                continue;
            }
            // 是否返回本级分类
            if ($articleCatId === $parentId && !$isLayer) {
                continue;
            }
            // 限制分类显示深度
            if (!is_null($limitLevel) && $level > $limitLevel) {
                break;
            }
            $value->setAttribute('level', $level);
            $tree[] = $value->toArray();
            // 需要返回本级分类时保留列表数据,否则引起树的重复,并且需要自增层级
            if (true == $isLayer) {
                $isLayer = false;
                $level++;
                continue;
            }
            // 删除已使用数据,减少查询次数
            unset($list[$key]);
            if ($value->getAttribute('children_total') > 0) {
                self::setArticleCatTree($tree, $articleCatId, $list, $limitLevel, $isLayer, $level + 1);
            }
        }
        return $tree;
    }

}
