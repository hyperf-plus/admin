<?php
declare(strict_types=0);
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/21
 * Time: 18:54
 */

namespace App\Library\WeChat;

use App\Library\WeChat\Request\CustomRequest;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\CoroutineHandler;
use Hyperf\Guzzle\HandlerStackFactory;
use Overtrue\Socialite\Providers\AbstractProvider;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use EasyWeChat\openPlatform\Application;

class WeChatOpen
{
    private static Application $uniqueInstance;


    /**
     * @return Application
     */
    public static function getInstance()
    {
        if (self::$uniqueInstance === null) {
            make(WeChatOpen::class);
        }
        return self::$uniqueInstance;
    }

    private function __clone()
    {
    }

    /**
     * WeChatOpen constructor.
     * @param ContainerInterface $container
     * @param CustomRequest $customRequest
     */
    public function __construct(ContainerInterface $container,CustomRequest $customRequest)
    {
        $config = config("wechat");
        $wechat = $config['open'];
        $wechat['log'] = $config['log'];
        /** @var Application $app */
        $app = Factory::openPlatform($wechat);
        $config = $app['config']->get('http', []);
        $config['use_pool'] = true;
        $config['handler'] = $container->get(HandlerStackFactory::class)->create();
        HasHttpRequests::setDefaultOptions([]);
        $app->rebind('http_client', new Client($config));
        $app->rebind('cache', $container->get(CacheInterface::class));
        $app->rebind('request', $customRequest);
        $app['guzzle_handler'] = new CoroutineHandler();
        // 设置 OAuth 授权的 Guzzle 配置
        AbstractProvider::setGuzzleOptions([
            'http_errors' => false,
            'handler' => HandlerStack::create(new CoroutineHandler()),
        ]);
        self::$uniqueInstance = $app;
        unset($app);
        return self::$uniqueInstance;
    }

}