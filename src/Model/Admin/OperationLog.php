<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
namespace HPlus\Admin\Model\Admin;

use HPlus\Admin\Model\Model;
use Hyperf\Database\Model\Relations\BelongsTo;

class OperationLog extends Model
{
    public static $methodColors = [
        'GET' => 'green',
        'POST' => 'yellow',
        'PUT' => 'blue',
        'DELETE' => 'red',
    ];

    public static $methods = [
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH',
        'LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE',
    ];

    protected $fillable = ['user_id', 'path', 'runtime', 'method', 'ip', 'input'];

    protected $casts = [
        'created_at' => 'Y-m-d H:i:s',
        'updated_at' => 'Y-m-d H:i:s',
    ];

    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('admin.database.operation_log_table'));
        parent::__construct($attributes);
    }

    /**
     * Log belongs to users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Administrator::class);
    }
}
