<?php


use App\Entity\UserInfo;
use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

if (!function_exists('RequestInterface')) {
    function request(): RequestInterface
    {
        return getContainer(RequestInterface::class);
    }
}

if (!function_exists('response')) {
    function response(): ResponseInterface
    {
        return getContainer(ResponseInterface::class);
    }
}


if (!function_exists('tree_2_paths')) {
    function tree_2_paths($tree, $pre_key = '', $id_key = 'value', $children_key = 'children')
    {
        $arr_paths = [];
        foreach ($tree as $node) {
            $now_key = $pre_key ? $pre_key . '-' . $node[$id_key] : $node[$id_key];
            if (!empty($node['children']) && is_array($node['children'])) {
                $arr = tree_2_paths($node['children'], $now_key, $id_key, $children_key);
                $arr_paths = array_merge($arr_paths, $arr);
            } else {
                $arr_paths[$now_key] = $node[$id_key];
            }
        }

        return $arr_paths;
    }
}

if (!function_exists('generate_tree')) {
    function generate_tree(array $array, $pid_key = 'pid', $id_key = 'id', $children_key = 'children', $callback = null)
    {
        if (!$array) {
            return [];
        }
        //第一步 构造数据
        $items = [];
        foreach ($array as $value) {
            if ($callback && is_callable($callback)) {
                $callback($value);
            }
            $items[$value[$id_key]] = $value;
        }
        //第二部 遍历数据 生成树状结构
        $tree = [];
        foreach ($items as $key => $value) {
            //如果pid这个节点存在
            if (isset($items[$value[$pid_key]])) {
                $items[$value[$pid_key]][$children_key][] = &$items[$key];
            } else {
                $tree[] = &$items[$key];
            }
        }

        return $tree;
    }
}

if (!function_exists('array_sort_by_value_length')) {
    function array_sort_by_value_length($arr, $sort_order = SORT_DESC)
    {
        $keys = array_map('strlen', $arr);
        array_multisort($keys, $sort_order, $arr);

        return $arr;
    }
}

if (!function_exists('array_sort_by_key_length')) {
    function array_sort_by_key_length($arr, $sort_order = SORT_DESC)
    {
        $keys = array_map('strlen', array_keys($arr));
        array_multisort($keys, $sort_order, $arr);

        return $arr;
    }
}

if (!function_exists('array_map_recursive')) {
    function array_map_recursive(callable $func, array $data)
    {
        $result = [];
        foreach ($data as $key => $val) {
            $result[$key] = is_array($val) ? array_map_recursive($func, $val) : call($func, [$val]);
        }

        return $result;
    }
}
if (!function_exists('array_merge_node')) {
    function array_merge_node($arr, $node, $key)
    {
        $box = [];
        foreach ($arr as $each) {
            $box[$each[$key] . '-'] = $each;
        }
        if (isset($node[0])) {
            foreach ($node as $n) {
                $box[$n[$key] . '-'] = $n;
            }
        } else {
            $box[$node[$key] . '-'] = $node;
        }

        return array_values($box);
    }
}
if (!function_exists('array_change_v2k')) {
    /**
     * 将二维数组二维某列的key值作为一维的key
     *
     * @param array $arr 原始数组
     * @param string $column key
     */
    function array_change_v2k(&$arr, $column)
    {
        if (empty($arr)) {
            return;
        }
        $new_arr = [];
        foreach ($arr as $val) {
            $new_arr[$val[$column]] = $val;
        }
        $arr = $new_arr;
    }
}

if (!function_exists('getUserInfo')) {
    function getUserInfo(): UserInfo
    {
        /** @var SessionInterface $session */
        $session = getSession();
        if ($session == null) {
            return new UserInfo();
        }
        $userInfo = $session->get(UserInfo::class);
        if ($userInfo instanceof UserInfo) {
            return $userInfo;
        }
        return new UserInfo();
    }
}

if (!function_exists('select_options')) {
    function select_options($api, array $kws)
    {
        $ret = [];
        $chunk = array_chunk($kws, 100);
        foreach ($chunk as $part) {
            //$ret = array_merge($ret, call_self_api($api, ['kw' => implode(',', $part)]));
        }
        return $ret;
    }
}

if (!function_exists('mergeArray')) {
    function mergeArray($array)
    {
        $result = [];
        foreach ($array as $item) {
            $result = array_merge($result, $item);
        }
        return $result;
    }
}


