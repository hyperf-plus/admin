<?php
declare(strict_types=1);

namespace Mzh\Admin\Controller\Api;

use Exception;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Admin\Library\Auth;
use Mzh\Admin\Model\Config;
use Mzh\Admin\Traits\GetApiList;
use Mzh\Admin\Traits\GetApiUI;
use Mzh\Admin\Validate\ConfigValidation;
use Mzh\Swagger\Annotation\ApiController;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\GetApi;
use Mzh\Swagger\Annotation\Path;
use Mzh\Swagger\Annotation\PostApi;
use Psr\Http\Message\ResponseInterface;

/**
 * @ApiController(tag="后台-通用配置模块")
 */
class Cconf
{
    protected $modelClass = Config::class;
    protected $validateClass = ConfigValidation::class;

    use GetApiUI;
    use GetApiList;

    /**
     * @GetApi(path="detail/{name}",summary="获取编辑表单配置",security=true)
     * @Path(key="name")
     * @throws Exception
     */
    public function detail($name)
    {
        $conf = Config::query()->where('name', $name)->first();
        if (!$conf || !$conf->rules) {
            throw new BusinessException(1000, '通用配置未找到 ' . $name);
        }
        $rules = $this->formOptionsConvert($conf->rules, true, false, false, $conf->value);
        $compute_map = $this->formComputeConfig($rules);
        return $this->json([
            'form' => $rules,
            'compute_map' => (object)$compute_map,
        ]);
    }

    /**
     * @PostApi(path="detail/{name}",summary="编辑表单配置",security=true)
     * @Path(key="name")
     * @throws Exception
     */
    public function updateDetail($name)
    {
        $conf = Config::query()->where('name', $name)->first();
        if (empty($conf)) {
            throw new BusinessException(1000, '数据不存在！');
        }
        if ($name == 'permissions') {
            $newOpenApi = $this->request->post('open_api', []);
            foreach (array_diff($newOpenApi, $conf->value['open_api']) as $url) {
                getContainer(Auth::class)->setIgnore($url);
            }
            #动态删除
            foreach (array_diff($conf->value['open_api'], $newOpenApi) as $url) {
                getContainer(Auth::class)->removeIgnore($url);
            }
        }

        $conf->fill(['value' => $this->request->all()])->save();
        return $this->json('保存成功！');
    }

}
