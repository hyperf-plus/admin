<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Article;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Mzh\Validate\Annotations\Validation;

class ArticleService
{

    /**
     * @Inject()
     * @var ArticleCatService
     */
    protected  $articleCatService;

    /**
     * 获取文章列表
     * @Validation(mode="Article",scene="list",field="data",filter=true)
     * @param array $data
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws Exception
     */
    public function list(array $data, $page = 0, $pageSize = 25)
    {
        // 排序方式
        $data['order_type'] ??= 'desc';
        $data['order_field'] ??= 'article_id';

        $data['page_no'] ??= $page;
        $data['page_size'] ??= $pageSize;

        // 获取分类Id,包括子分类
        $catIdList = [];
        if (isset($data['article_cat_id'])) {
            $catIdList[] = (int)$data['article_cat_id'];
            $articleCat = $this->articleCatService->list($data);
            foreach ($articleCat as $value) {
                $catIdList[] = $value['article_cat_id'];
            }
        }
        //添加分类条件
        $model = Article::query();
        if (is_array($catIdList) && !empty($catIdList)) {
            $model->whereIn('article.article_cat_id', $catIdList);
        }
        if (isset($data['title']) && !empty($data['title'])) $model->where('article.title', 'LIKE', '%' . $data['title'] . '%');
        // 后台管理搜索
        if (getUserInfo()->getType() == 1) {
            if (isset($data['status'])) $model->where('article.status', intval($data['status']));
            if (isset($data['is_top'])) $model->where('article.is_top', intval($data['is_top']));
            if (isset($data['keywords']) && !empty($data['keywords'])) $model->where('article.keywords', 'like', '%' . $data['keywords'] . '%');
        } else {
            $model->where('article.status', 1);
        }
        // 文章前后台置顶处理
        $model->orderBy('article.' . $data['order_field'], $data['order_type']);
        if (!empty($data['order_field']) && isClientAdmin()) {
            $model->orderBy('article.is_top', 'desc');
        }
        $result = $model->with('getArticleCat')->paginate($data['page_size'], ['article_id', 'article_cat_id', 'title', 'image', 'source', 'source_url', 'keywords', 'description', 'url', 'target', 'page_views', 'is_top', 'status', 'create_time', 'update_time'], '', $data['page_no']);
        if ($result->isEmpty()) {
            return ['total_result' => 0];
        }
        return ['items' => $result->items(), 'total_result' => $result->total()];
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
