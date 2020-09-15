<?php
declare(strict_types=1);

namespace HPlus\Admin\Model\Admin;

use Hyperf\Database\Model\Relations\BelongsTo;
use HPlus\Admin\Model\Model;

class OperationLog extends Model
{
    protected $fillable = ['user_id', 'path','runtime', 'method', 'ip', 'input'];

    public static $methodColors = [
        'GET'    => 'green',
        'POST'   => 'yellow',
        'PUT'    => 'blue',
        'DELETE' => 'red',
    ];

    public static $methods = [
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH',
        'LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE',
    ];

    protected $casts = [
        'created_at'=>"Y-m-d H:i:s",
        'updated_at'=>"Y-m-d H:i:s",
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('admin.database.operation_log_table'));
        parent::__construct($attributes);
    }

    /**
     * Log belongs to users.
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(Administrator::class);
    }
}