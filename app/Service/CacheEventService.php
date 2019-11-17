<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

class CacheEventService
{
    /**
     * @Inject
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

//    public function flushCache($userId)
//    {
//       $this->dispatcher->dispatch(new DeleteListenerEvent('CommonAuth-update', [$userId]));
//        return true;
//    }

    /**
     * @CacheEvict(prefix="CommonAuth", value="_#{module}_#{groupId}")
     * @param string $module
     * @param int $groupId
     * @return bool
     */
    public function flushCache(string $module,int $groupId)
    {
        return true;
    }
}