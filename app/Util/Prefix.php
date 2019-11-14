<?php

namespace App\Util;

class Prefix
{

    public static function getLoginErrCount($unique)
    {
        return 'system-user-err-count::' . $unique;
    }

    public static function authNodes($unique)
    {
        return 'system-user-auth::' . $unique;
    }

}