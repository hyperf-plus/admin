<?php
declare(strict_types=1);

use App\Entity\Tenant;
use App\Entity\UserInfo;
use Hyperf\Utils\Context;
use Psr\Http\Message\ServerRequestInterface;

function userMd5($str)
{
    return md5($str);
}

if (!function_exists('autoHidSubstr')) {
    function isClientAdmin()
    {
        return getUserInfo()->getType() == 1;
    }
}


if (!function_exists('autoHidSubstr')) {
    /**
     * 智能字符串模糊化
     * @param string $str 被模糊的字符串
     * @param int $len 模糊的长度
     * @return string
     */
    function autoHidSubstr($str, $len = 3)
    {
        if (empty($str)) {
            return null;
        }
        $str = (string)$str;

        $sub_str = mb_substr($str, 0, 1, 'utf-8');
        for ($i = 0; $i < $len; $i++) {
            $sub_str .= '*';
        }
        if (mb_strlen($str, 'utf-8') <= 2) {
            $str = $sub_str;
        }
        $sub_str .= mb_substr($str, -1, 1, 'utf-8');
        return $sub_str;
    }
}

if (!function_exists('isEmptyParam')) {
    /**
     * 判断是否存在并且不为空
     * @param $param
     * @return bool
     */
    function isEmptyParam($param)
    {
        return (isset($param) && !empty($param));
    }
}

function getTenant(): Tenant
{
    $request = Context::get(Tenant::class);
    if ($request instanceof Tenant) {
        return $request;
    }
    return new Tenant();
}

/**
 * @return UserInfo
 */
function getUserInfo(): UserInfo
{
    $request = Context::get(UserInfo::class);
    if ($request instanceof UserInfo) {
        return $request;
    }
    return new UserInfo();
}

if (!function_exists('getRandStr')) {
    /**
     * 产生数字与字母混合随机字符串
     * @param int $len 数值长度,默认6位
     * @return string
     */
    function getRandStr($len = 6)
    {
        $chars = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
            'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2',
            '3', '4', '5', '6', '7', '8', '9',
        ];
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = '';
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }

        return $output;
    }
}

if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        try {
            /**
             * @var ServerRequestInterface $request
             */
            $request = Context::get(ServerRequestInterface::class);
            $ip_addr = $request->getHeaderLine('x-forwarded-for');
            if (verifyIp($ip_addr)) {
                return $ip_addr;
            }
            $ip_addr = $request->getHeaderLine('remote-host');
            if (verifyIp($ip_addr)) {
                return $ip_addr;
            }
            $ip_addr = $request->getHeaderLine('x-real-ip');
            if (verifyIp($ip_addr)) {
                return $ip_addr;
            }
            $ip_addr = $request->getServerParams()['remote_addr'] ?? '0.0.0.0';
            if (verifyIp($ip_addr)) {
                return $ip_addr;
            }
        } catch (Throwable $e) {
            return '0.0.0.0';
        }
        return '0.0.0.0';
    }
}
if (!function_exists('verifyIp')) {
    function verifyIp($realip)
    {
        return filter_var($realip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}
if (!function_exists('p')) {
    function p($val, $title = null, $starttime = '')
    {
        print_r('[ ' . date("Y-m-d H:i:s") . ']:');
        if ($title != null) {
            print_r("[" . $title . "]:");
        }
        print_r($val);
        print_r("\r\n");
    }
}
if (!function_exists('uuid')) {
    function uuid($length)
    {
        if (function_exists('random_bytes')) {
            $uuid = bin2hex(random_bytes($length));
        } else if (function_exists('openssl_random_pseudo_bytes')) {
            $uuid = bin2hex(openssl_random_pseudo_bytes($length));
        } else {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $uuid = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
        }
        return $uuid;
    }
}
if (!function_exists('filterEmoji')) {
    function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        $cleaned = strip_tags($str);
        return htmlspecialchars(($cleaned));
    }
}


function convertUnderline($str)
{
    $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
        return strtoupper($matches[2]);
    }, $str);
    return $str;
}

/*
    * 驼峰转下划线
    */
function humpToLine($str)
{
    $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
        return '_' . strtolower($matches[0]);
    }, $str);
    return $str;
}

function convertHump(array $data)
{
    $result = [];
    foreach ($data as $key => $item) {
        if (is_array($item) || is_object($item)) {
            $result[humpToLine($key)] = convertHump((array)$item);
        } else {
            $result[humpToLine($key)] = $item;
        }
    }
    return $result;
}