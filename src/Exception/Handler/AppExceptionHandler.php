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
namespace HPlus\Admin\Exception\Handler;

use HPlus\Admin\Exception\BusinessException;
use HPlus\Admin\Exception\UserLoginException;
use HPlus\Admin\Exception\ValidateException;
use HPlus\Permission\Exception\PermissionException;
use HPlus\UI\Exception\ValidateException as UIValidateException;
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
            case $throwable instanceof PermissionException:
                $response = $response->withStatus(401);
                break;
            case $throwable instanceof ValidateException:
            case $throwable instanceof UIValidateException:
                $response = $response->withStatus(422);
                break;
        }
        $this->stopPropagation();
        return $response->withBody(new SwooleStream(json_encode([
            'code' => $throwable->getCode(),
            'error' => $throwable->getMessage(),
        ], 256)));
    }

    public function isValid(Throwable $throwable): bool
    {
        if (
            $throwable instanceof BusinessException ||
            $throwable instanceof UserLoginException ||
            $throwable instanceof PermissionException ||
            $throwable instanceof ValidateException ||
            $throwable instanceof UIValidateException
        )
            return true;
        return false;
    }
}
