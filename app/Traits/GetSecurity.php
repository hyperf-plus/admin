<?php

namespace App\Traits;

use App\Entity\Principal;
use Hyperf\Utils\Context;

trait GetSecurity
{

    public function getPrincipal(): ?Principal
    {
        return Context::get(Principal::class);
    }

    /**
     * 当前账户有指定角色中的任意一个时返回true
     * @param string|array $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        $principal = $this->getPrincipal();
        if ($principal instanceof Principal) {
            return $principal->hasAnyRole($roles);
        } else {
            return false;
        }
    }

    /**
     * 是否拥有权限
     * @param string|array $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions)
    {
        $principal = $this->getPrincipal();
        if ($principal instanceof Principal) {
            return $principal->hasAnyPermission($permissions);
        } else {
            return false;
        }
    }

    /**
     * 是否已经登录
     * @return bool
     */
    public function isAuthenticated()
    {
        $principal = $this->getPrincipal();
        if ($principal == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * IP地址是否符合支持10.0.0.0/16这种
     * @param array $ips
     * @return bool
     */
    public function hasIpAddress($ips)
    {
        $ip = $request = getClientIp();
        if (is_array($ips)) {
            foreach ($ips as $oneip) {
                if ($oneip == $ip) return true;
                $exip = explode("/", $oneip);
                $mask = $exip[1] ?? null;
                if ($mask != null) {
                    if (self::netMatch($ip, $exip[0], $mask)) return true;
                }
            }
            return false;
        } elseif (is_string($ips)) {
            if ($ips == $ip) return true;
            $exip = explode("/", $ips);
            $mask = $exip[1] ?? null;
            if ($mask != null) {
                return self::netMatch($ip, $exip[0], $mask);
            } else {
                return false;
            }
        } else {
            return false;
        }

    }
    private static function netMatch($client_ip, $server_ip, $mask)
    {
        $mask1 = 32 - $mask;
        return ((ip2long($client_ip) >> $mask1) == (ip2long($server_ip) >> $mask1));
    }

}
