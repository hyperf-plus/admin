<?php


namespace App\Util;


use App\Exception\FileException;

class Node
{
    /**
     * 忽略控制名的前缀
     * @var array
     */
    private static $ignoreController = [
        'Controller', 'Admin/LoginController', 'IndexController'
    ];

    /**
     * 忽略控制的方法名
     * @var array
     */
    private static $ignoreAction = [
        '__construct', 'isPost', 'isGet', 'getAdmin', 'getAdminID', 'getAdminName', 'getAdminRole'
    ];

    /**
     * @param $dir
     * @return array
     * @throws \ReflectionException
     */
    public static function getClassNodes($dir)
    {
        if (!is_dir($dir)) {
            throw new FileException('目录不存在！');
        }
        $nodes = [];
        self::eachController($dir, function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            list($node, $comment) = [str_replace('Controller', '', trim($prenode, '/')), $reflection->getDocComment()];
            $nodes[$node] = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $comment));
            if (stripos($nodes[$node], '@') !== false) {
                $nodes[$node] = '';
            }
        });
        return $nodes;
    }

    /**
     *
     * @param $dir
     * @param $callable
     * @throws \ReflectionException
     */
    public static function eachController($dir, $callable)
    {
        $app_namespace = config('app_namespace');
        foreach (self::scanDir($dir) as $file) {
            if (!preg_match("|/Controller/(.+)\.php$|", strtr($file, '\\', '/'), $matches)) continue;
            $controller = $matches[1];
            foreach (self::$ignoreController as $ignore) {
                if (stripos($controller, $ignore) === 0) {
                    continue 2;
                }
            }
            $class = substr(strtr($app_namespace . $matches[0], '/', '\\'), 0, -4);
            if (class_exists($class)) {
                call_user_func($callable, new \ReflectionClass($class), $controller);
            }
        }
    }

    /**
     * 获取方法节点列表
     * @param string $dir 控制器根路径
     * @return array
     * @throws \ReflectionException
     */
    public static function getMethodNodes($dir)
    {
        $nodes = [];
        self::eachController($dir, function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $action = $method->getName();
                foreach (self::$ignoreAction as $ignore) if (stripos($action, $ignore) === 0) continue 2;
                $node = str_replace('Controller', '', $prenode) . '/' . $action;
                $nodes[$node] = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $method->getDocComment()));
                if (stripos($nodes[$node], '@') !== false) $nodes[$node] = '';
            }
        });
        return $nodes;
    }


    /**
     * @param $dir
     * @param array $data
     * @param string $ext
     * @return array
     */
    public static function scanDir($dir, $data = [], $ext = 'php')
    {
        foreach (scandir($dir) as $curr) {
            if (strpos($curr, '.') !== 0) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $curr);
                if (is_dir($path)) {
                    $data = array_merge($data, self::scanDir($path));
                } elseif (pathinfo($path, PATHINFO_EXTENSION) === $ext) {
                    $data[] = $path;
                }
            }
        }
        return $data;
    }

}