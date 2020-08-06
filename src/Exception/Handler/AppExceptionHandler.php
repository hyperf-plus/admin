<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Mzh\Admin\Exception\Handler;


use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Exception\NotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\UserLoginException;
use Mzh\Admin\Exception\ValidateException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        switch (true) {
            case $throwable instanceof NotFoundException:
                $response = $response->withStatus(404);
                break;
            case $throwable instanceof BusinessException:
                $response = $response->withStatus(403);
                break;
            case $throwable instanceof UserLoginException:
                $response = $response->withStatus(401);
                break;
            case $throwable instanceof ValidateException:
                $response = $response->withStatus(422);
                break;
            case $throwable->getCode() == 0:
            case $throwable instanceof \Exception:
                $response = $response->withStatus(403);
                break;
        }
        return $response->withBody(new SwooleStream(json_encode([
            "code" => $throwable->getCode() == 0 ? 1000 : $throwable->getCode(),
            "error" => $throwable->getMessage(),
        ], 256)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
