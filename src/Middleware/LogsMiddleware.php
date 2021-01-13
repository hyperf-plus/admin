<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
namespace HPlus\Admin\Middleware;

use HPlus\Admin\Facades\Admin;
use HPlus\Admin\Model\Admin\OperationLog;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AuthMiddleware.
 */
class LogsMiddleware implements MiddlewareInterface
{
    protected $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config->get('admin.operation_log');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (! $this->config['enable']
            || ! $this->inAllowedMethods($request->getMethod())
            || $this->inExceptArray($request)
        ) {
            return $handler->handle($request);
        }
        $time = $this->getMicroTime();
        $response = $handler->handle($request);
        $time = round(($this->getMicroTime() - $time) * 1000, 1);
        try {
            OperationLog::create([
                'user_id' => Admin::user()->getId(),
                'path' => substr($request->getUri()->getPath(), 0, 255),
                'method' => $request->getMethod(),
                'ip' => get_client_ip(),
                'runtime' => $time,
                'header' => $request->getHeaders(),
                'request' => array_merge($request->getQueryParams(), (array) $request->getParsedBody()),
                'result' => json_decode($response->getBody()->getContents(), true),
            ]);
        } catch (\Throwable $exception) {
            // pass
            Logger()->info('可能您的log日志表不是最新的， 请执行 php bin/hyperf.php admin:db -l 查看升级命令');
        }
        return $response;
    }

    public function is(...$except)
    {
        $path = request()->path();
        foreach ($except as $pattern) {
            if (Str::is($pattern, rawurldecode($path))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Whether requests using this method are allowed to be logged.
     *
     * @param string $method
     *
     * @return bool
     */
    protected function inAllowedMethods($method)
    {
        $allowedMethods = collect($this->config['allowed_methods'])->filter();
        if ($allowedMethods->isEmpty()) {
            return true;
        }
        return $allowedMethods->map(function ($method) {
            return strtoupper($method);
        })->contains($method);
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->config['except'] as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            $methods = [];
            if (Str::contains($except, ':')) {
                [$methods, $except] = explode(':', $except);
                $methods = explode(',', $methods);
            }
            $methods = array_map('strtoupper', $methods);
            if ($this->is($except)
                && (empty($methods) || in_array($request->getMethod(), $methods))) {
                return true;
            }
        }
        return false;
    }

    private function getMicroTime()
    {
        [$usec, $sec] = explode(' ', microtime());
        return (float) $usec + (float) $sec;
    }
}
