<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/13
 */

namespace App\Validate;


class Goods  extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'goods_id'          => 'integer|gt:0',
        'goods_category_id' => 'require|integer|gt:0',
        'name'              => 'require|max:200',
        'short_name'        => 'max:50',
        'product_name'      => 'max:100',
        'goods_code'        => 'max:50|unique:goods,goods_code,0,goods_id',
        'goods_spu'         => 'max:50',
        'goods_sku'         => 'max:50',
        'bar_code'          => 'max:60',
        'brand_id'          => 'integer|egt:0',
        'store_qty'         => 'integer|egt:0',
        'market_price'      => 'require|float|gt:0|regex:^\d+(\.\d{1,2})?$',
        'shop_price'        => 'require|float|gt:0|regex:^\d+(\.\d{1,2})?$',
        'integral_type'     => 'in:0,1',
        'give_integral'     => 'checkIntegral:integral_type',
        'is_integral'       => 'integer|egt:0',
        'least_sum'         => 'integer|egt:0|checkLeast:purchase_sum',
        'purchase_sum'      => 'integer|egt:0',
        'keywords'          => 'max:255',
        'description'       => 'max:255',
        'content'           => 'require',
        'attachment'        => 'array',
        'video'             => 'array',
        'unit'              => 'max:10',
        'is_recommend'      => 'in:0,1',
        'is_new'            => 'in:0,1',
        'is_hot'            => 'in:0,1',
        'goods_type_id'     => 'require|integer|gt:0',
        'sort'              => 'integer|between:0,255',
        'status'            => 'in:0,1',
        'goods_attr_item'   => 'array',
        'goods_spec_menu'   => 'array|requireWith:goods_spec_item|requireWith:spec_image',
        'goods_spec_item'   => 'array',
        'spec_image'        => 'array',
        'is_delete'         => 'in:0,1,2',
        'exclude_id'        => 'integer|gt:0',
        'is_goods_spec'     => 'in:0,1',
        'is_spec_image'     => 'in:0,1',
        'is_postage'        => 'require|in:0,1',
        'measure'           => 'requireIf:is_postage,0|float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'measure_type'      => 'requireIf:is_postage,0|in:0,1,2',
        'goods_type'        => 'in:integral,recommend,new,hot',
        'page_no'           => 'integer|gt:0',
        'page_size'         => 'integer|gt:0',
        'order_type'        => 'in:asc,desc',
        'order_field'       => 'in:goods_id,goods_code,name,shop_price,store_qty,sales_sum,sort,is_integral,is_recommend,is_new,is_hot,create_time',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'goods_id'          => '商品编号',
        'goods_category_id' => '商品分类编号',
        'name'              => '商品名称',
        'short_name'        => '商品短名称',
        'product_name'      => '商品促销名',
        'goods_code'        => '商品货号',
        'goods_spu'         => '商品SPU',
        'goods_sku'         => '商品SKU',
        'bar_code'          => '商品条码',
        'brand_id'          => '商品品牌编号',
        'store_qty'         => '商品库存',
        'market_price'      => '商品市场价',
        'shop_price'        => '商品本店价',
        'integral_type'     => '赠送积分结算方式',
        'give_integral'     => '商品赠送积分',
        'is_integral'       => '积分可抵扣额',
        'least_sum'         => '最少起订量',
        'purchase_sum'      => '限购数量',
        'keywords'          => '商品关键词',
        'description'       => '商品描述',
        'content'           => '商品详情描述',
        'attachment'        => '商品相册',
        'video'             => '商品短视频',
        'unit'              => '商品计量单位',
        'is_recommend'      => '是否推荐',
        'is_new'            => '是否新品',
        'is_hot'            => '是否热卖',
        'goods_type_id'     => '商品模型编号',
        'sort'              => '排序值',
        'status'            => '商品上下架状态',
        'goods_attr_item'   => '商品属性列表',
        'goods_spec_menu'   => '商品规格菜单数据',
        'goods_spec_item'   => '商品规格列表',
        'spec_image'        => '商品规格图',
        'is_delete'         => '是否放入回收站',
        'exclude_id'        => '商品排除Id',
        'is_goods_spec'     => '是否获取商品规格列表',
        'is_spec_image'     => '是否获取商品规则图',
        'is_postage'        => '是否包邮',
        'measure'           => '商品计量',
        'measure_type'      => '商品计量方式',
        'page_no'           => '页码',
        'page_size'         => '每页数量',
        'order_type'        => '排序方式',
        'order_field'       => '排序字段',
        'goods_type'        => '商品类型',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'unique'     => [
            'goods_code' => 'require|max:50',
            'exclude_id',
        ],
        'set'        => [
            'goods_id'   => 'require|integer|gt:0',
            'goods_category_id',
            'name',
            'short_name',
            'product_name',
            'goods_code' => 'require|max:50',
            'goods_spu',
            'goods_sku',
            'bar_code',
            'brand_id',
            'store_qty',
            'is_postage',
            'measure',
            'measure_type',
            'least_sum',
            'purchase_sum',
            'market_price',
            'shop_price',
            'integral_type',
            'give_integral',
            'is_integral',
            'keywords',
            'description',
            'content',
            'attachment',
            'video',
            'unit',
            'is_recommend',
            'is_new',
            'is_hot',
            'goods_type_id',
            'sort',
            'status',
            'goods_attr_item',
            'goods_spec_menu',
            'goods_spec_item',
            'spec_image',
        ],
        'item'       => [
            'goods_id' => 'require|integer|gt:0',
        ],
        'del'        => [
            'goods_id'  => 'require|arrayHasOnlyInts',
            'is_delete' => 'require|in:0,1,2',
        ],
        'integral'   => [
            'goods_id'    => 'require|arrayHasOnlyInts',
            'is_integral' => 'require|integer|egt:0',
        ],
        'recommend'  => [
            'goods_id'     => 'require|arrayHasOnlyInts',
            'is_recommend' => 'require|in:0,1',
        ],
        'new'        => [
            'goods_id' => 'require|arrayHasOnlyInts',
            'is_new'   => 'require|in:0,1',
        ],
        'hot'        => [
            'goods_id' => 'require|arrayHasOnlyInts',
            'is_hot'   => 'require|in:0,1',
        ],
        'shelves'    => [
            'goods_id' => 'require|arrayHasOnlyInts',
            'status'   => 'require|in:0,1',
        ],
        'admin_list' => [
            'goods_id'          => 'arrayHasOnlyInts',
            'exclude_id'        => 'arrayHasOnlyInts',
            'goods_category_id' => 'integer|gt:0',
            'keywords'          => 'max:200',
            'goods_code'        => 'max:60', // code,spu,sku,bar
            'brand_id'          => 'arrayHasOnlyInts',
            'store_qty'         => 'arrayHasOnlyInts:zero|length:2',
            'is_postage'        => 'in:0,1',
            'is_integral',
            'is_recommend',
            'is_new',
            'is_hot',
            'status',
            'is_delete',
            'is_goods_spec',
            'is_spec_image',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'type_list'  => [
            'goods_category_id',
            'goods_type',
            'brand_id',
            'shop_price' => 'array|length:2',
            'page_no',
            'page_size',
        ],
        'index_list' => [
            'goods_category_id' => 'integer|egt:0',
            'keywords'          => 'max:100',
            'is_postage'        => 'in:0,1',
            'is_integral',
            'shop_price'        => 'array|length:2',
            'bar_code',
            'brand_id'          => 'arrayHasOnlyInts',
            'goods_spec_item',
            'goods_attr_item',
            'page_no'           => 'integer|gt:0',
            'page_size'         => 'integer|gt:0',
            'order_type',
            'order_field'       => 'in:goods_id,sales_sum,comment_sum,shop_price,create_time',
        ],
        'sort'       => [
            'goods_id' => 'require|integer|gt:0',
            'sort'     => 'require|integer|between:0,255',
        ],
        'suggest'    => [
            'keywords' => 'require|max:255',
        ],
        'copy'       => [
            'goods_id' => 'require|integer|gt:0',
        ],
    ];

    /**
     * 验证最少起订量
     * @access public
     * @param  mixed $value 验证数据
     * @param  mixed $rule  验证规则(purchase_sum)
     * @param  array $data  全部数据
     * @return mixed
     */
    public function checkLeast($value, $rule, $data)
    {
        if (!empty($data[$rule]) && $value > $data[$rule]) {
            return $this->field['least_sum'] . '必须大于等于 ' . $this->field[$rule];
        }

        return true;
    }

    /**
     * 验证商品积分
     * @access public
     * @param  mixed $value 验证数据
     * @param  mixed $rule  验证规则(integral_type)
     * @param  array $data  全部数据
     * @return mixed
     */
    public function checkIntegral($value, $rule, $data)
    {
        if (!$this->check($data, [$rule => 'require|in:0,1'])) {
            return $this->getError();
        }

        $integral = ['give_integral' => $value];
        switch ($data[$rule]) {
            case 0:
                if ($this->check($integral, ['give_integral' => 'float|between:0,100|regex:^\d+(\.\d{1,2})?$'])) {
                    return true;
                }
                break;

            case 1:
                if ($this->check($integral, ['give_integral' => 'integer|egt:0'])) {
                    return true;
                }
                break;
        }

        return $this->getError();
    }
}
