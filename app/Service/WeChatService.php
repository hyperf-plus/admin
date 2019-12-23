<?php
declare(strict_types=1);

namespace App\Service;


use App\Library\WeChat\WeChatOpen;
use App\Model\WeChatConfig;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use EasyWeChat\OpenPlatform\Application;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\CoroutineHandler;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Overtrue\Socialite\Providers\AbstractProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\SimpleCache\CacheInterface;

class WeChatService
{
    /**
     * @Inject()
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @Inject()
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @Cacheable(prefix="name_get_appid")
     * @param $ToUserName
     * @return string
     */
    public static function getAppId($ToUserName)
    {
        return Db::table('wechat_config')->where('user_name', $ToUserName)->value('authorizer_appid');
    }

    public function getAccount($appid = 'wxa72f08f7960fd824')
    {
        $config = WeChatConfig::findFromCache($appid);
        return WeChatOpen::getInstance()->officialAccount($appid, (string)$config->getAttribute('authorizer_refresh_token'));
    }

    public function getMini($AppId = 'wxf05f87cfa723790b'): \EasyWeChat\MiniProgram\Application
    {
        $config = [
            'app_id' => 'wxf05f87cfa723790b',
            'secret' => '07e9fe0d9cd1ba7aaedbfa954e133177',
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => __DIR__ . '/wechat.log',
            ],
        ];
        $app = Factory::miniProgram($config);
        $config = $app['config']->get('http', []);
        $config['use_pool'] = true;
        $config['handler'] = $this->container->get(HandlerStackFactory::class)->create();
        HasHttpRequests::setDefaultOptions([]);
        $app->rebind('http_client', new Client($config));
        $app->rebind('cache', $this->container->get(CacheInterface::class));
        $app->rebind('request', $this->container->get(RequestInterface::class));
        $app['guzzle_handler'] = new CoroutineHandler();
        // 设置 OAuth 授权的 Guzzle 配置
        AbstractProvider::setGuzzleOptions([
            'http_errors' => false,
            'handler' => HandlerStack::create(new CoroutineHandler()),
        ]);
        return $app;
    }

    public function getMiniProgram($appid = 'wxf05f87cfa723790b')
    {


        //         'appid'      => 'wxf05f87cfa723790b',
        //        'appsecret'  => '07e9fe0d9cd1ba7aaedbfa954e133177',
        $config = WeChatConfig::findFromCache($appid);
        return WeChatOpen::getInstance()->miniProgram($appid, (string)$config->getAttribute('authorizer_refresh_token'));
    }

}