<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Mzh\Admin\Listener;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\AfterWorkerStart;
use Psr\Container\ContainerInterface;

/**
 * @Listener
 */
class AuthInitListener implements ListenerInterface
{
    /**
     * @var StdoutLoggerInterface
     */
    private $logger;
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function listen(): array
    {
        return [
            AfterWorkerStart::class,
        ];
    }

    public function process(object $event)
    {
//        if ($event instanceof AfterWorkerStart && $event->workerId == 0) {
//           Timer::after(1000,function (){
//               $this->logger->info("开始搜集权限注解");
//               $timer = new RunTimes();
//               $timer->start();
//               $auth = getContainer(Auth::class);
//               $auth->restart();
//               $this->logger->info("搜集完成，用时" . $timer->spent());
//           });
//        }
    }
}
