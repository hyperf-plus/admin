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

namespace HPlus\Admin\Exception\Handler;

use HPlus\Admin\Exception\BusinessException;
use HPlus\Admin\Exception\UserLoginException;
use HPlus\Admin\Exception\ValidateException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
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
            case $throwable instanceof BusinessException:
                $response = $response->withStatus(403);
                break;
            case $throwable instanceof UserLoginException:
                $response = $response->withStatus(401);
                break;
            case $throwable instanceof ValidateException:
                $response = $response->withStatus(422);
                break;
        }
        return $response->withBody(new SwooleStream(json_encode([
            "code" => $throwable->getCode(),
            "error" => $throwable->getMessage(),
        ], 256)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
