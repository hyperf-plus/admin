<?php

namespace HPlus\Admin\Model\Admin;

use HPlus\Admin\Model\Model;
use \Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\HttpServer\Request;
use Hyperf\Utils\Str;
use HPlus\Admin\Traits\ModelTree;


class Permission extends Model
{

    //AdminBuilder,
    use ModelTree {
        ModelTree::boot as treeBoot;
    }

    /**
     * @var array
     */
    protected $fillable = ['name', 'slug', 'path', 'parent_id'];

    /**
     * @var array
     */
    protected $casts = [
        'created_at' => "Y-m-d H:i:s",
        'updated_at' => "Y-m-d H:i:s",
        'path' => "array",
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('admin.database.permissions_table'));
        $this->setTitleColumn('name');
        parent::__construct($attributes);
    }

    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'role_id');
    }

    /**
     * If request should pass through the current permission.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function shouldPassThrough(Request $request): bool
    {
        if (empty($this->http_method) && empty($this->http_path)) {
            return true;
        }

        $method = $this->http_method;


        $web_matches = array_map(function ($path) use ($method) {
            $path = trim(config('admin.route.prefix'), '/') . $path;
            if (Str::contains($path, ':')) {
                list($method, $path) = explode(':', $path);
                $method = explode(',', $method);
            }
            return compact('method', 'path');
        }, explode("\n", $this->http_path));

        $api_matches = array_map(function ($path) use ($method) {
            $path = trim(config('admin.route.api_prefix'), '/') . $path;
            if (Str::contains($path, ':')) {
                list($method, $path) = explode(':', $path);
                $method = explode(',', $method);
            }
            return compact('method', 'path');
        }, explode("\n", $this->http_path));

        $matches = array_merge($web_matches, $api_matches);


        foreach ($matches as $match) {
            if ($this->matchRequest($match, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * filter \r.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getHttpPathAttribute($path)
    {
        return str_replace("\r\n", "\n", $path);
    }

    /**
     * If a request match the specific HTTP method and path.
     *
     * @param array $match
     * @param Request $request
     *
     * @return bool
     */
    protected function matchRequest(array $match, Request $request): bool
    {


        if ($match['path'] == '/') {
            $path = '/';
        } else {
            $path = trim($match['path'], '/');
        }

        if (!$request->is($path)) {
            return false;
        }

        $method = collect($match['method'])->filter()->map(function ($method) {
            return strtoupper($method);
        });

        return $method->isEmpty() || $method->contains($request->method());
    }

    /**
     * @param $method
     */
    public function setHttpMethodAttribute($method)
    {
        if (is_array($method)) {
            $this->attributes['http_method'] = implode(',', $method);
        }
    }

    /**
     * @param $method
     *
     * @return array
     */
    public function getHttpMethodAttribute($method)
    {
        if (is_string($method)) {
            return array_filter(explode(',', $method));
        }

        return $method;
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected function boot(): void
    {
        parent::boot();
//        static::deleted(function ($model) {
//            $model->roles()->detach();
//        });
    }
}
