<?php
declare(strict_types=1);


namespace App\Entity;


use Hyperf\Utils\Context;

class EntityBean
{
    public function __construct($array = [])
    {
        foreach ($array as $item => $value) {
            $method = 'set' . convertUnderline($item);
            if (method_exists($this, $method)) {
                call_user_func([$this, $method], $value);
            }
        }
    }
}