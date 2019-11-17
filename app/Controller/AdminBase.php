<?php


namespace App\Controller;

use App\Exception\StatusException;
use App\Exception\SystemException;

/**
 * Class AuthController
 * @package App\Controller
 */
class AdminBase extends Controller
{

    /**
     * s     * 方法路由器
     * @var array
     */
    private static $route;

    protected static function initMethod()
    {
    }

    /**
     * 默认入口控制器
     * @return array
     * @throws SystemException
     */
    public function index()
    {

        $class = str_replace("App\\Controller\\Api\\v1", '', static::class);
        // 获取控制器方法路由
        if (!isset(self::$route[$class])) {
            self::$route[$class] = static::initMethod();
        }
        $method = $this->request->query('method');
        if (!array_key_exists($method, self::$route[$class])) {
            throw new StatusException($method . '模式,尝试查找的模型不存在', 404);
        }
        $result = null;
        $callback = self::$route[$class][$method];
        // 路由定义中如果数组[1]不存在,则表示默认对应model模型
        $class = "App\Model" . $class;
        if (!class_exists($class, false)) throw new SystemException('模块不存在', 500);
        $CLASS = make($class);
        $response = [];
        $response['status'] = 200;
        $response['message'] = 'success';
        $response['data'] = [];
        $params = $this->request->all();
        unset($params['appkey']);
        unset($params['token']);
        unset($params['timestamp']);
        unset($params['format']);
        unset($params['method']);
        unset($params['sign']);
        try {
            if (method_exists($class, $callback[0])) {
                $result = call_user_func([$CLASS, $callback[0]], $params);
            } else {
                throw new SystemException('method成员方法不存在');
            }
            if (is_array($result)) {
                $response['data'] = (array)$result;
            }
        } catch (\Throwable $e) {
            //拦截所有报错，统一返回
            $response['status'] = $e->getCode();
            $response['message'] = $e->getMessage();
        }
        //处理完成，这里可以增加日志记录功能


        // 记录日志
//            $logError = $response['message'];
//            self::$auth->saveLog($this->getAuthUrl(), $this->request, $result, get_called_class(), $logError);


        return $response;
    }
}