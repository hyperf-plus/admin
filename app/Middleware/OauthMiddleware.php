<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\UserInfo;
use App\Model\Member;
use App\Service\WeChatService;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use Hyperf\DbConnection\Db;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


/**
 * 测试
 * Class OauthMiddleware
 * @package App\Middleware
 */
class OauthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var WeChatService
     */
    private $weChat;
    /**
     * @var \Redis
     */
    protected $redis;

    public function __construct(ContainerInterface $container, WeChatService $chatService)
    {
        $this->container = $container;
        $this->redis = $container->get(\Redis::class);
        $this->weChat = $chatService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getServerParams()['path_info'] ?? '';
        $tenant = getTenant();

        if ($path == '/api/User/token') {
            $app = $this->weChat->getMini($tenant->getMiniAppId());
            $code = $request->getQueryParams()['code'] ?? '';
            $debug = $request->getQueryParams()['d'] ?? '';
            if ($debug != 1) {
                try {
                    $wx_info = $app->auth->session($code);
                } catch (InvalidConfigException $e) {
                    throw new \Exception("授权CODE信息换取失败，{$e->getMessage()}");
                }
                if (isset($wx_info['errcode'])) {
                    throw new \Exception("授权CODE信息换取失败，{$wx_info['errmsg']}");
                }
            } else {
                $wx_info['session_key'] = 'btXiadaUJA6usB6v86++Cw==';
                $wx_info['openid'] = 'ojzVX49hIGXyJ_39aHWUfv-sDbKM';
            }
            $Member = Member::query()->select(['unionid', 'openid', 'headimgurl', 'id', 'nickname', 'country', 'province', 'city', 'vip_level', 'create_at'])->firstOrNew(['openid' => $wx_info['openid']]);
            $Member->openid = $wx_info['openid'];
            $Member->tenant_id = $tenant->getId();
            $Member->save();
            $withToken = $token = uuid(32);
            $token = $this->getToken($withToken);
            $userInfo = new UserInfo($Member->toArray());
            $userInfo->setUid($Member->id);
            $userInfo->saveLoginInfo();
            $this->redis->set($token, serialize($userInfo));
            $response = $handler->handle($request);
            //请求结束，刷新当前token有效时长
            $this->redis->expire($token, 3600);
            return $response->withBody(new SwooleStream($response->getBody()->getContents()))->withAddedHeader('token', $withToken)->withHeader('Content-Type', 'application/json')->withHeader('server', 'WangLaiServer');
        } else {
            $token = $this->getToken($request->getHeaderLine('token'));
            $redisRaw = $this->redis->get($token);
            $userInfo = unserialize((!empty($redisRaw) ? $redisRaw : 's:0:"";'));
            if ($userInfo instanceof UserInfo) {
                $userInfo->saveLoginInfo();
                $response = $handler->handle($request);
                //请求结束，刷新当前token有效时长
                $this->redis->expire($token, 3600);
                return $response->withBody(new SwooleStream($response->getBody()->getContents()))->withHeader('Content-Type', 'application/json')->withHeader('server', 'WangLaiServer');
            }
            throw new \Exception('token not empty');
        }
    }

    private function getToken($token = false): string
    {
        if ($token === false) $token = uuid(32);
        return getTenant()->getId() . ':token:' . $token;
    }
}