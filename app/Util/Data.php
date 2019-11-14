<?php


namespace App\Util;


class Data
{
    public static function toTree(array $list, string $id = 'id', string $pid = 'pid', string $sub = 'subs'): array
    {
        $tops = [];//顶级菜单
        $subs = [];//二级菜单
        foreach ($list as $item) {
            if (0 == $item[$pid]) {
                $item[$sub] = [];
                $tops[$item[$id]] = $item;
            } else {
                $subs[] = $item;
            }
        }

        foreach ($tops as &$top) {
            foreach ($subs as $item) {
                if ($item[$pid] == $top[$id]) {
                    $top[$sub][] = $item;
                }
            }
        }
        unset($top);

        return array_values($tops);
    }
}