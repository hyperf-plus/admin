<?php


namespace App\Annotations;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target("ALL")
 */
class CorpAuthorize extends AbstractAnnotation
{
    /**
     * @var string
     */
    public string $value='';
    /**
     * @var array|string
     */
    public  $roles = [];
    /**
     * @var array|string
     */
    public $ips = [];
    /**
     * @var bool
     */
    public bool $all = false;
    /**
     * @var bool
     */
    public bool $deny = false;
}