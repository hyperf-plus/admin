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

namespace App\Exception\Handler;

use App\Exception\AccessDeniedException;
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
        if ($throwable instanceof AccessDeniedException) {
            return $response->withStatus(200)->withHeader('Content-Type','application/json; charset=utf-8')->withBody(new SwooleStream(json_encode(['status' => $throwable->getCode(), 'message' => $throwable->getMessage()], 256)));
        }
        return $response->withStatus(200)->withHeader('Content-Type','application/json; charset=utf-8')->withBody(new SwooleStream(json_encode(['status' => $throwable->getCode(), 'message' => $throwable->getMessage()], 256)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
