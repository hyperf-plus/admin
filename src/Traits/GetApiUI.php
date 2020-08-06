<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Str;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Helper\DbHelper\GetQueryHelper;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\GetApi;
use Mzh\Swagger\Annotation\PostApi;
use Psr\Http\Message\ResponseInterface;

trait GetApiUI
{
    use GetApiBase;
    use GetUiBase;
    use GetQueryHelper;

    protected $options = null;

    /**
     * @GetApi(summary="获取UI配置",security=true)
     * @throws \Exception
     */
    public function info()
    {
        $model = $this->getView();
        $this->options = $model->scaffoldOptions();
        $tableHeader = array_values(array_filter($this->getListHeader(), function ($item) {
            return !($item['hidden'] ?? false);
        }));
        $filter = $this->getListFilters();
        $actions = $this->options['table']['rowActions'] ?? [];
        $actions = $this->buttonConfigConvert($actions);
        $topActions = $this->options['table']['topActions'] ?? [];
        $topActions = $this->buttonConfigConvert($topActions);
        $batchButtons = $this->options['table']['batchButtons'] ?? [];
        $batchButtons = $this->buttonConfigConvert($batchButtons);
        $enum = $this->options['table']['enum'] ?? [];
        $resource = $this->getCalledSource(true);
        $tabs = $this->options['table']['tabs'] ?? [];
        $info = [
            'filterRule' => $filter,
            'tableHeader' => $tableHeader,
            'rowActions' => $actions,
            'tableTabs' => is_callable($tabs) ? $tabs() : $tabs,
            'options' => [
                'form_path' => $this->options['form_path'] ?? '',
                'rowChangeApi' => "/{$resource[1]}/rowchange/{" . $this->getPk() . "}",
                'batchButtons' => $batchButtons,
                'enum' => $enum,
                'createAble' => $this->options['createAble'] ?? true,
                'exportAble' => $this->options['exportAble'] ?? true,
                'defaultList' => $this->options['defaultList'] ?? true,
                'importAble' => $this->options['importAble'] ?? false,
                'topActions' => $topActions,
                'tableOptions' => [
                    'style' => $this->options['table']['style'] ?? 'list',
                    'group' => $this->options['table']['group'] ?? [],
                ],
                'noticeAble' => !empty($this->options['notices'] ?? []),
            ],
        ];
        if (method_exists($this, '_info_before')) {
            $info = $this->_info_before($info);
        }
        return $this->json($info);
    }

//    /**
//     * 表单拉取接口
//     * @GetApi(summary="获取编辑表单配置",security=true)
//     */
//    public function edit()
//    {
//        $id = $this->request->query($this->getPk(), 0);
//        $record = $this->_detail($id);
//        $this->callback('response_before', $id, $record);
//        $form = $this->formOptionsConvert([], false, true, false, $record);
//        return $this->json(array_merge($this->formResponse($id, $form)));
//    }
//    /**
//     * @PostApi(path="edit",summary="UI界面更新单条信息",security=true)
//     * @Body(scene="update",security=true)
//     * @return ResponseInterface
//     * @throws ValidateException|BusinessException
//     */
//    public function edit_update()
//    {
//        return $this->json($this->_update());
//    }

    /**
     * @GetApi(summary="获取编辑表单配置",security=true)
     * @throws \Exception
     */
    public function form()
    {
        $record = [];
        $edit = false;
        $id = $this->request->query($this->getPk(), 0);
        if ($id > 0) {
            $record = $this->_detail($id);
            $edit = true;
        }
        $this->callback('response_before', $id, $record);
        $form = $this->formOptionsConvert([], false, $edit, false, $record);
        return $this->json(array_merge($this->formResponse($id, $form)));
    }

    /**
     * @PostApi(path="form",summary="创建单条信息",security=true)
     * @Body(scene="update",security=true)
     * @return ResponseInterface
     * @throws ValidateException|BusinessException
     */
    public function form_updateOrcreate()
    {
        return $this->json($this->_updateOrCreate());
    }


    protected function getCalledSource($get_arr = false)
    {
        $uri = $this->getRequestUri();
        $parts = array_filter(explode('/', $uri));
        if ($get_arr) {
            return array_values($parts);
        }
        return implode('.', $parts);
    }

    protected function getRequestUri()
    {
        $http_request = getContainer(RequestInterface::class);
        return $http_request->getServerParams()['request_uri'] ?? '';
    }

    /**
     * 获取列表的搜索项
     */
    protected function getListFilters()
    {
        if (empty($this->options['filter'])) {
            return [];
        }
        $form_fields = $this->getFormFieldMap();
        $form_options = $this->options['form'] ?? [];
        $filter_options = [];
        foreach ($this->options['filter'] as $key => $item) {
            $filter_option_key = is_array($item) ? $key : str_replace('%', '', $item);
            $field_extra = explode('|', $filter_option_key);
            $field = $field_extra[0];
            $form_option = [];
            if (isset($form_fields[$field]) && isset($form_options[$form_fields[$field]])) {
                $filter_option_key = $form_fields[$field];
                $form_option = is_array($form_options[$form_fields[$field]]) ? $form_options[$form_fields[$field]] : [];
                if (isset($form_option['rule'])) {
                    unset($form_option['rule']);
                }
            }
            $filter_option = is_array($item) ? $item : [];
            if (!empty($field_extra[1])) {
                $filter_option_key = "{$field}|{$field_extra[1]}";
            }
            $filter_options[$filter_option_key] = array_merge($form_option, $filter_option);
            if (!isset($filter_options[$filter_option_key]['search_type']) && is_string($item)) {
                $search_type = 'eq';
                if (Str::startsWith($item, '%') !== false) {
                    $search_type = 'prefix_like';
                }
                if (Str::endsWith($item, '%') !== false) {
                    $search_type = 'suffix_like';
                }
                if (Str::startsWith($item, '%') !== false && Str::endsWith($item, '%') !== false) {
                    $search_type = 'full_like';
                }
                if (strpos(($filter_options[$filter_option_key]['type'] ?? ''), 'range') !== false) {
                    $search_type = 'between';
                }
                $filter_options[$filter_option_key]['search_type'] = $search_type;
            }
        }
        unset($form_options);
        return $this->formOptionsConvert($filter_options, true, false, true);
    }


}