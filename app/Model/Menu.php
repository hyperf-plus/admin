<?php
declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Query\Builder;
use Mzh\JwtAuth\Auth;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';
    protected $fillable = ['parent_id', 'name', 'alias', 'module', 'icon', 'remark', 'type', 'url', 'params', 'target', 'is_navi', 'sort'];



}