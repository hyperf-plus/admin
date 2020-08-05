<?php


use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Mzh\Admin\Entity\UserInfo;

if (!function_exists('request')) {
    function request(): RequestInterface
    {
        return getContainer(RequestInterface::class);
    }
}

if (!function_exists('get_current_date')) {
    function get_current_date($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }
}

if (!function_exists('isSystemRole')) {
    function isSystemRole(): bool
    {
        return true;
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
if (!function_exists('data_desensitization')) {
    /**
     * 数据脱敏
     *
     * @param string $string 需要脱敏值
     * @param int $first_length 保留前n位
     * @param int $last_length 保留后n位
     * @param string $re 脱敏替代符号
     *
     * @return bool|string
     * 例子:
     * data_desensitization('18811113683', 3, 4); //188****3683
     * data_desensitization('王富贵', 0, 1); //**贵
     */
    function data_desensitization($string, $first_length = 0, $last_length = 0, $re = '*')
    {
        if (empty($string) || $first_length < 0 || $last_length < 0) {
            return $string;
        }
        $str_length = mb_strlen($string, 'utf-8');
        $first_str = mb_substr($string, 0, $first_length, 'utf-8');
        $last_str = mb_substr($string, -$last_length, $last_length, 'utf-8');
        if ($str_length <= 2 && $first_length > 0) {
            $replace_length = $str_length - $first_length;

            return $first_str . str_repeat($re, $replace_length > 0 ? $replace_length : 0);
        } elseif ($str_length <= 2 && $first_length == 0) {
            $replace_length = $str_length - $last_length;

            return str_repeat($re, $replace_length > 0 ? $replace_length : 0) . $last_str;
        } elseif ($str_length > 2) {
            $replace_length = $str_length - $first_length - $last_length;

            return $first_str . str_repeat("*", $replace_length > 0 ? $replace_length : 0) . $last_str;
        }
        if (empty($string)) {
            return $string;
        }
    }
}


