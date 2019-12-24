<?php

namespace App\Annotations;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target("ALL")
 */
class AuthUpEvict extends AbstractAnnotation
{
    /**
     * @var int
     */
    public $group = 0;

    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->bindMainProperty('group', $value);
    }
}