<?php


namespace App\Model\Entity;


class EntityBase
{
    public function __construct(array $array = [])
    {
        foreach ($array as $item => $value) {
            if (method_exists($this, 'set' . ucwords($item))) {
                call_user_func([$this, 'set' . ucwords($item)], $value);
            }
        }
    }
}